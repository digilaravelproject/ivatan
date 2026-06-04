# Delete Account Flow — API Documentation for Flutter

> **Version**: 1.0  
> **Last Updated**: 2026-06-04  
> **Stack**: Laravel 12 + Sanctum (Token Auth) + SoftDeletes

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Flow Diagram](#2-flow-diagram)
3. [API Endpoints](#3-api-endpoints)
   - [3.1 Request Account Deletion](#31-request-account-deletion)
   - [3.2 Restore Account Within 30 Days](#32-restore-account-within-30-days)
   - [3.3 Login (after deletion/restore)](#33-login-after-deletionrestore)
4. [Data Lifecycle](#4-data-lifecycle)
5. [Error Handling](#5-error-handling)
6. [Flutter Integration Checklist](#6-flutter-integration-checklist)
7. [Testing Guidance](#7-testing-guidance)

---

## 1. System Overview

When a user requests account deletion, the system **soft-deletes** the account for 30 days. During this window:

- **User cannot log in** — all Sanctum tokens are revoked immediately
- **User can restore** their account by providing email + password
- **Admin can restore** from the admin panel
- **After 30 days**, the system permanently deletes the user and all associated data via a daily cron job

```
Delete Request ──► Soft Delete ──► 30 Day Window ──► Auto Permanent Delete
                      │                   │
                      ▼                   ▼
              Tokens Revoked      User Can Restore
              Login Blocked       (email + password)
```

---

## 2. Flow Diagram

```
Flutter App                              Laravel Backend
───────────                              �──────────────

  │                                           │
  │  POST /api/v1/auth/delete-account         │
  │  (Bearer Token)                           │
  │ ──────────────────────────────────────►    │
  │                                           ├── Revoke ALL Sanctum tokens
  │                                           ├── $user->delete() (soft delete)
  │                                           │
  │  ◄──────────────────────────────────────  │
  │  { success: true, message: "..." }        │
  │                                           │
  │  ──  User cannot log in anymore ──        │
  │                                           │
  │  POST /api/v1/auth/restore-account        │
  │  { email, password }                      │
  │ ──────────────────────────────────────►    │
  │                                           ├── Verify email + password
  │                                           ├── Check deleted_at < 30 days
  │                                           ├── $user->restore()
  │                                           │
  │  ◄──────────────────────────────────────  │
  │  { success: true, message: "...", data }  │
  │                                           │
  │  POST /api/auth/login                     │
  │  (now user can login again)               │
  │ ──────────────────────────────────────►    │
  │  ◄──────────────────────────────────────  │
  │  { success: true, data: { token, user } } │
```

---

## 3. API Endpoints

### 3.1 Request Account Deletion

```
POST /api/v1/auth/delete-account
Authorization: Bearer <sanctum_token>
```

**Headers:**
```
Authorization: Bearer <sanctum_token>
Accept: application/json
```

**Request Body:** None required.

**Response `200 OK`:**
```json
{
  "success": true,
  "message": "Your account has been scheduled for deletion. It will be permanently deleted after 30 days. You can contact support to restore it within this window."
}
```

**Error Response `401` (unauthenticated):**
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

> **Note**: After this call, the user's Sanctum token is immediately revoked. The Flutter app should:
> 1. Clear stored token from secure storage
> 2. Redirect to login screen
> 3. Show a confirmation snackbar with the 30-day info

**Flutter Usage:**
```dart
Future<void> deleteAccount() async {
  try {
    final response = await http.post(
      Uri.parse('$baseUrl/api/v1/auth/delete-account'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    final body = jsonDecode(response.body);
    if (body['success'] == true) {
      // Clear local auth state
      await secureStorage.delete(key: 'auth_token');
      // Navigate to login
      Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(builder: (_) => const LoginScreen()),
        (route) => false,
      );
      // Show snackbar with message
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(body['message'])),
      );
    }
  } catch (e) {
    // Handle network errors
  }
}
```

---

### 3.2 Restore Account Within 30 Days

```
POST /api/v1/auth/restore-account
Content-Type: application/json
```

> **Note**: This endpoint does NOT require authentication (user is deleted and can't have a token). It uses email + password verification instead.

**Headers:**
```
Accept: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "user-password"
}
```

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `email` | string | **yes** | Must be a valid email format |
| `password` | string | **yes** | Must match the stored password |

**Response `200 OK` (account restored):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "username": "johndoe",
      "profile_photo_url": "https://..."
    }
  },
  "message": "Your account has been restored. Please log in to continue."
}
```

**Error Response `400` (past 30 days):**
```json
{
  "success": false,
  "message": "The 30-day restoration period has passed. Your account has been permanently deleted."
}
```

**Error Response `401` (wrong password):**
```json
{
  "success": false,
  "message": "Invalid credentials."
}
```

**Error Response `404` (no account found):**
```json
{
  "success": false,
  "message": "No account found with this email address."
}
```

**Flutter Usage:**

```dart
class RestoreAccountScreen extends StatefulWidget {
  @override
  _RestoreAccountScreenState createState() => _RestoreAccountScreenState();
}

class _RestoreAccountScreenState extends State<RestoreAccountScreen> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;

  Future<void> _restoreAccount() async {
    setState(() => _isLoading = true);
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/api/v1/auth/restore-account'),
        headers: {'Accept': 'application/json'},
        body: {
          'email': _emailController.text.trim(),
          'password': _passwordController.text,
        },
      );
      final body = jsonDecode(response.body);
      if (response.statusCode == 200 && body['success'] == true) {
        _showSuccess('Account restored! Please log in.');
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (_) => const LoginScreen()),
        );
      } else {
        _showError(body['message'] ?? 'Restore failed');
      }
    } catch (e) {
      _showError('Network error. Please try again.');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  void _showSuccess(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(msg), backgroundColor: Colors.green),
    );
  }

  void _showError(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(msg), backgroundColor: Colors.red),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Restore Account')),
      body: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            Text('Enter your email and password to restore your account.'),
            const SizedBox(height: 16),
            TextField(
              controller: _emailController,
              decoration: const InputDecoration(labelText: 'Email'),
              keyboardType: TextInputType.emailAddress,
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _passwordController,
              decoration: const InputDecoration(labelText: 'Password'),
              obscureText: true,
            ),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: _isLoading ? null : _restoreAccount,
              child: _isLoading
                  ? const CircularProgressIndicator()
                  : const Text('Restore Account'),
            ),
          ],
        ),
      ),
    );
  }
}
```

---

### 3.3 Login (after deletion/restore)

The existing login endpoint works as expected. Key behavior:

| User State | Can Login? | Behavior |
|------------|-----------|----------|
| Active account | ✅ Yes | Normal login |
| Soft-deleted (< 30 days) | ❌ No | Returns `401 Invalid credentials` |
| Soft-deleted (> 30 days) | ❌ No | Returns `401 Invalid credentials` (purged) |
| Restored (< 30 days) | ✅ Yes | Normal login — gets new token |
| Permanently deleted | ❌ No | Returns `401 Invalid credentials` |

```
POST /api/auth/login
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "user-password"
}
```

**Response `200 OK`:**
```json
{
  "success": true,
  "data": {
    "user": { ... },
    "token": "1|abc123..."
  }
}
```

---

## 4. Data Lifecycle

### Timeline

```
Day 0:  User requests deletion
        ├── Soft delete (deleted_at = now)
        ├── All Sanctum tokens revoked
        └── User cannot log in

Day 1–29: 30-day restoration window
        ├── User CAN restore via email + password
        ├── Admin CAN restore from admin panel
        └── Data still exists in database (soft-deleted)

Day 30:  Auto-purging (daily cron: account:purge-expired)
        ├── Polymorphic records cleaned (notifications, tokens, roles, activity log)
        ├── LiveChatGroups reassigned or deleted
        ├── Media files deleted from storage
        ├── User force-deleted
        └── All cascade-enabled tables cleaned automatically by DB
```

### What Happens to User Data

| Data Type | After Soft Delete | After 30 Days |
|-----------|------------------|---------------|
| User profile | Soft-deleted (hidden) | **Permanently deleted** |
| Posts, Stories, Reels | Soft-deleted (via cascade) | **Permanently deleted** |
| Comments, Likes | Soft-deleted (via cascade) | **Permanently deleted** |
| Orders | Buyer info SET NULL | `seller_id` set to null |
| Chat messages | Orphaned (sender set to null) | Orphaned in system |
| Live Chat Groups | If owned, reassigned or deleted | Reassigned or deleted |
| Notifications | Hard-deleted (polymorphic — manual) | **Permanently deleted** |
| Sanctum Tokens | Hard-deleted (polymorphic) | **Permanently deleted** |
| Roles/Permissions | Hard-deleted (polymorphic) | **Permanently deleted** |
| Profile Photo | File preserved | **File deleted from storage** |

---

## 5. Error Handling

### Error Response Format

All endpoints return errors in a consistent format:

```json
{
  "success": false,
  "message": "Descriptive error message"
}
```

### Restore Account Error Codes

| Scenario | HTTP Status | `message` |
|----------|-------------|-----------|
| Email not found | 500 | `No account found with this email address.` |
| Wrong password | 500 | `Invalid credentials.` |
| Account already active | 500 | `Your account is already active.` |
| Past 30-day window | 500 | `The 30-day restoration period has passed. Your account has been permanently deleted.` |
| Missing email field | 422 | `The email field is required.` |
| Invalid email format | 422 | `The email must be a valid email address.` |
| Missing password field | 422 | `The password field is required.` |

### Flutter Error Handler Template

```dart
Future<Map<String, dynamic>> handleAccountRestore({
  required String email,
  required String password,
}) async {
  try {
    final response = await http.post(
      Uri.parse('$baseUrl/api/v1/auth/restore-account'),
      headers: {'Accept': 'application/json'},
      body: {'email': email, 'password': password},
    );

    final body = jsonDecode(response.body);

    if (response.statusCode == 200 && body['success'] == true) {
      return body;
    }

    if (response.statusCode == 422) {
      // Validation error — extract first error message
      final errors = body['errors'] as Map?;
      final firstError = errors?.values.firstWhere(
        (v) => v is List && v.isNotEmpty,
        orElse: () => ['Validation failed'],
      );
      throw ApiException((firstError as List).first);
    }

    throw ApiException(body['message'] ?? 'Restore failed');
  } on SocketException {
    throw ApiException('No internet connection');
  } on TimeoutException {
    throw ApiException('Request timed out');
  }
}

class ApiException implements Exception {
  final String message;
  ApiException(this.message);

  @override
  String toString() => message;
}
```

---

## 6. Flutter Integration Checklist

### Account Deletion Screen

- [ ] Show a confirmation dialog before calling delete endpoint
- [ ] Inform user about the 30-day restoration window
- [ ] Call `POST /api/v1/auth/delete-account` with Bearer token
- [ ] On success: clear secure storage (token), navigate to login
- [ ] Show snackbar with the 30-day info message

### Account Restore Screen

- [ ] Provide email + password form
- [ ] Validate inputs before sending
- [ ] Call `POST /api/v1/auth/restore-account` (no auth header)
- [ ] Handle all error scenarios:
  - Email not found → "No account found with this email."
  - Wrong password → "Invalid credentials."
  - Past 30 days → "Restoration period has passed."
  - Validation errors → Show inline field errors
- [ ] On success: show message, navigate to login screen

### Login Screen (after restore)

- [ ] After restore, user logs in normally via `POST /api/auth/login`
- [ ] Gets new Sanctum token
- [ ] Normal app flow resumes

### Settings / Profile Page

- [ ] Add "Delete Account" button in settings
- [ ] Add "Restore Account" link on login screen (for deleted users)
- [ ] Show clear messaging about data retention policy

### UI/UX Recommendations

| Screen | Component | Copy |
|--------|-----------|------|
| Settings | Delete Account button | "Delete My Account" (red/destructive) |
| Confirmation | AlertDialog | "Are you sure? Your account will be deactivated immediately. You have 30 days to restore it." |
| Login | Help text | "Forgot password?" + "Restore deleted account?" |
| Restore | Form screen | "Enter your email and password to restore your account." |
| Success | Snackbar | "Account restored! Please log in." |
| Past 30 days | Error dialog | "Sorry, the 30-day restoration period has passed." |

### State Management Strategy

```dart
enum AccountStatus { active, deleted, restoring, restored }

class AuthProvider extends ChangeNotifier {
  AccountStatus _status = AccountStatus.active;
  String? _errorMessage;

  AccountStatus get status => _status;
  String? get errorMessage => _errorMessage;

  Future<void> deleteAccount(String token) async {
    _status = AccountStatus.deleted;
    notifyListeners();

    // Call API, clear storage, etc.
    await storage.delete(key: 'auth_token');
  }

  Future<bool> restoreAccount(String email, String password) async {
    _status = AccountStatus.restoring;
    notifyListeners();

    try {
      // Call /api/v1/auth/restore-account
      _status = AccountStatus.restored;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      _status = AccountStatus.deleted;
      notifyListeners();
      return false;
    }
  }
}
```

---

## 7. Testing Guidance

### Manual Testing Checklist

| Test Case | Steps | Expected Result |
|-----------|-------|----------------|
| Delete account | Logged-in user calls delete endpoint | Account soft-deleted, token revoked, login blocked |
| Login after delete | Try to login with deleted account credentials | `401` error with "Invalid credentials" |
| Restore within 30 days | Call restore with correct email + password | Account restored, can login again |
| Restore with wrong password | Call restore with wrong password | `500` error "Invalid credentials." |
| Restore after 30 days | Manually set `deleted_at` to 31 days ago, try restore | `500` error "Restoration period has passed." |
| Login after restore | Login with restored account | Success, new token issued |
| Admin view trashed | Admin goes to `/admin/users/trashed` | Sees list of soft-deleted users |
| Admin restore | Admin restores user from admin panel | User can login again |

### Automated Test Coverage

The test suite at `tests/Feature/DeleteAccountFlowTest.php` covers:

| # | Test | What It Verifies |
|---|------|-----------------|
| 1 | User can request account deletion | Soft-delete works, API returns success |
| 2 | Deleted user cannot login | Login blocked after soft-delete |
| 3 | Deleted user tokens are revoked | Old bearer token returns 401 |
| 4 | Unauthenticated user blocked | 401 without token |
| 5 | User can restore within 30 days | Restore API works, user un-trashed |
| 6 | User can login after restore | Login works with new token |
| 7 | Wrong password fails restore | Error returned |
| 8 | Non-existent email fails restore | Error returned |
| 9 | Active account restore fails | Error — not deleted |
| 10 | Past 30-day restore fails | Error — past window |
| 11 | Admin can view trashed users | Admin page shows deleted users |
| 12 | Purge command deletes expired users | forceDelete called for >30 days |
| 13 | Purge skips recent deletions | <30 days users preserved |
| 14 | Purge cleans up notifications | Polymorphic records deleted |
| 15 | Purge dry-run preserves data | --dry-run does not delete |

---

## Quick Reference: All Endpoints

| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| `POST` | `/api/v1/auth/delete-account` | ✅ Bearer | Request account deletion |
| `POST` | `/api/v1/auth/restore-account` | ❌ None | Restore deleted account (email + password) |
| `POST` | `/api/auth/login` | ❌ None | Standard login (blocked if deleted) |
