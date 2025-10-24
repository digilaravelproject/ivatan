<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $ad_package_id
 * @property string $title
 * @property string|null $description
 * @property array<array-key, mixed>|null $media
 * @property string $status
 * @property Carbon|null $start_at
 * @property Carbon|null $end_at
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdImpression> $impressions
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int|null $impressions_count
 * @property-read \App\Models\AdPackage|null $package
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdPayment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereAdPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereMedia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereUserId($value)
 * @mixin \Eloquent
 * @property int|null $media_id
 * @property int|null $interest_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereInterestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereMediaId($value)
 */
	class Ad extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $ad_id
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ad $ad
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression whereAdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression whereUserId($value)
 * @mixin \Eloquent
 */
	class AdImpression extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property numeric $price
 * @property string $currency
 * @property int $reach_limit
 * @property int $duration_days
 * @property array<array-key, mixed>|null $targeting
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ad> $ads
 * @property-read int|null $ads_count
 * @property mixed $title
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereDurationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereReachLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereTargeting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class AdPackage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $ad_id
 * @property int $user_id
 * @property numeric $amount
 * @property string $currency
 * @property string $status
 * @property string|null $razorpay_order_id
 * @property string|null $razorpay_payment_id
 * @property string|null $razorpay_signature
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ad $ad
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereAdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereRazorpayOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereRazorpayPaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereRazorpaySignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereUserId($value)
 * @mixin \Eloquent
 */
	class AdPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $admin_id
 * @property string $action
 * @property string|null $target_type
 * @property int|null $target_id
 * @property array<array-key, mixed>|null $payload
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $admin
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereUserAgent($value)
 * @mixin \Eloquent
 */
	class AdminLog extends \Eloquent {}
}

namespace App\Models\Chat{
/**
 * @property int $id
 * @property string $uuid
 * @property string $type
 * @property string|null $name
 * @property int|null $owner_id
 * @property array<array-key, mixed>|null $meta
 * @property string|null $last_message_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chat\UserChatMessage|null $lastMessage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Chat\UserChatMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Chat\UserChatParticipant> $participants
 * @property-read int|null $participants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereLastMessageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereUuid($value)
 * @mixin \Eloquent
 */
	class UserChat extends \Eloquent {}
}

namespace App\Models\Chat{
/**
 * @property int $id
 * @property string $uuid
 * @property int $chat_id
 * @property int $sender_id
 * @property string|null $content
 * @property string $message_type
 * @property string|null $attachment_path
 * @property array<array-key, mixed>|null $meta
 * @property int|null $reply_to_message_id
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chat\UserChat $chat
 * @property-read UserChatMessage|null $replyTo
 * @property-read User $sender
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereAttachmentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereMessageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereReplyToMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereUuid($value)
 * @mixin \Eloquent
 */
	class UserChatMessage extends \Eloquent {}
}

namespace App\Models\Chat{
/**
 * @property int $id
 * @property int $chat_id
 * @property int $user_id
 * @property int $is_admin
 * @property int|null $last_read_message_id
 * @property \Illuminate\Support\Carbon $joined_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chat\UserChat $chat
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereLastReadMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereUserId($value)
 * @mixin \Eloquent
 */
	class UserChatParticipant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $commentable_type
 * @property int $commentable_id
 * @property int|null $parent_id
 * @property string $body
 * @property string $status
 * @property int $like_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $commentable
 * @property-read bool $has_liked
 * @property-read int|null $likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Like> $likes
 * @property-read Comment|null $parent
 * @property-read \App\Models\UserPost|null $post
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereLikeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUserId($value)
 * @mixin \Eloquent
 */
	class Comment extends \Eloquent {}
}

namespace App\Models\Ecommerce{
/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int|null $order_id
 * @property string $type
 * @property string $name
 * @property string $phone
 * @property string $address_line1
 * @property string|null $address_line2
 * @property string $city
 * @property string $state
 * @property string $country
 * @property string $postal_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereAddressLine1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereAddressLine2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereUuid($value)
 * @mixin \Eloquent
 */
	class UserAddress extends \Eloquent {}
}

namespace App\Models\Ecommerce{
/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserCartItem> $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart whereUuid($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserCartItem> $items_old
 * @property-read int|null $items_old_count
 */
	class UserCart extends \Eloquent {}
}

namespace App\Models\Ecommerce{
/**
 * @property int $id
 * @property string $uuid
 * @property int $cart_id
 * @property int $seller_id
 * @property string|null $item_type
 * @property int|null $item_id
 * @property int $quantity
 * @property string $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ecommerce\UserCart $cart
 * @property-read Model|\Eloquent|null $item
 * @property-read Model|\Eloquent|null $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereUuid($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $product_old
 * @property-read \App\Models\Ecommerce\UserService|null $service
 */
	class UserCartItem extends \Eloquent {}
}

namespace App\Models\Ecommerce{
/**
 * @property int $id
 * @property string $uuid
 * @property int $buyer_id
 * @property numeric $total_amount
 * @property string $status
 * @property string $payment_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ecommerce\UserAddress|null $address
 * @property-read User $buyer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserOrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Ecommerce\UserPayment|null $payment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserPayment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Ecommerce\UserShipping|null $shipping
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereBuyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereUuid($value)
 * @mixin \Eloquent
 */
	class UserOrder extends \Eloquent {}
}

namespace App\Models\Ecommerce{
/**
 * @property int $id
 * @property string $uuid
 * @property int $order_id
 * @property int $seller_id
 * @property string|null $item_type
 * @property int|null $item_id
 * @property int $quantity
 * @property string $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $item_model
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereUuid($value)
 * @mixin \Eloquent
 */
	class UserOrderItem extends \Eloquent {}
}

namespace App\Models\Ecommerce{
/**
 * @property int $id
 * @property string $uuid
 * @property int $order_id
 * @property string $gateway
 * @property numeric $amount
 * @property string $status
 * @property string|null $transaction_id
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ecommerce\UserOrder $order
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereUuid($value)
 * @mixin \Eloquent
 */
	class UserPayment extends \Eloquent {}
}

namespace App\Models\Ecommerce{
/**
 * @property int $id
 * @property string $uuid
 * @property int $seller_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property numeric $price
 * @property int $stock
 * @property string|null $cover_image
 * @property string $status
 * @property string|null $admin_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserCartItem> $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserProductImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserOrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read User $seller
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereAdminNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereUuid($value)
 * @mixin \Eloquent
 */
	class UserProduct extends \Eloquent {}
}

namespace App\Models\Ecommerce{
/**
 * @property int $id
 * @property int $product_id
 * @property string $image_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ecommerce\UserProduct $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class UserProductImage extends \Eloquent {}
}

namespace App\Models\Ecommerce{
/**
 * @property int $id
 * @property string $uuid
 * @property int $seller_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property numeric $price
 * @property string $status
 * @property string|null $admin_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserCartItem> $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserServiceImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserOrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read User $seller
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereAdminNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereUuid($value)
 * @mixin \Eloquent
 * @property string|null $cover_image
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereCoverImage($value)
 */
	class UserService extends \Eloquent {}
}

namespace App\Models\Ecommerce{
/**
 * @property int $id
 * @property int $service_id
 * @property string $image_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ecommerce\UserService $service
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class UserServiceImage extends \Eloquent {}
}

namespace App\Models\Ecommerce{
/**
 * @property int $id
 * @property string $uuid
 * @property int $order_id
 * @property string|null $provider
 * @property string|null $tracking_number
 * @property string $status
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ecommerce\UserOrder $order
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereTrackingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereUuid($value)
 * @mixin \Eloquent
 */
	class UserShipping extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $follower_id
 * @property int $following_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $follower
 * @property-read \App\Models\User $following
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower whereFollowerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower whereFollowingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Follower extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ad> $ads
 * @property-read int|null $ads_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interest whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interest whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interest whereUpdatedAt($value)
 */
	class Interest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $uuid
 * @property int $posted_by
 * @property string $title
 * @property string|null $description
 * @property string|null $company_name
 * @property string|null $location
 * @property int|null $salary_from
 * @property int|null $salary_to
 * @property string|null $employment_type
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $poster
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\View> $views
 * @property-read int|null $views_count
 * @method static \Database\Factories\JobFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereEmploymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job wherePostedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereSalaryFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereSalaryTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job withoutTrashed()
 * @mixin \Eloquent
 */
	class Job extends \Eloquent {}
}

namespace App\Models\Jobs{
/**
 * @property int $id
 * @property string $uuid
 * @property int $job_id
 * @property int $applicant_id
 * @property string|null $cover_message
 * @property string|null $resume_path
 * @property int|null $resume_media_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $applied_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $applicant
 * @property-read \App\Models\Jobs\UserJobPost $job
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereApplicantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereAppliedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereCoverMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereResumeMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereResumePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereUuid($value)
 * @mixin \Eloquent
 */
	class UserJobApplication extends \Eloquent {}
}

namespace App\Models\Jobs{
/**
 * @property int $id
 * @property string $uuid
 * @property int $employer_id
 * @property string $title
 * @property string $slug
 * @property string|null $company_name
 * @property string|null $company_website
 * @property string|null $company_logo
 * @property string $description
 * @property string|null $responsibilities
 * @property string|null $requirements
 * @property string|null $location
 * @property string|null $country
 * @property string $employment_type
 * @property numeric|null $salary_min
 * @property numeric|null $salary_max
 * @property string $currency
 * @property bool $is_remote
 * @property string $status
 * @property-read int|null $views_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Jobs\UserJobApplication> $applications
 * @property-read int|null $applications_count
 * @property-read User $employer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\View> $views
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost filter(array $filters = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereCompanyLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereCompanyWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereEmployerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereEmploymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereIsRemote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereRequirements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereResponsibilities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereSalaryMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereSalaryMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereViewsCount($value)
 * @mixin \Eloquent
 */
	class UserJobPost extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $likeable_type
 * @property int $likeable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $likeable
 * @property-read \App\Models\UserPost|null $post
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereLikeableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereLikeableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereUserId($value)
 * @mixin \Eloquent
 */
	class Like extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string|null $uuid
 * @property string $collection_name
 * @property string $name
 * @property string $file_name
 * @property string|null $mime_type
 * @property string $disk
 * @property string|null $conversions_disk
 * @property int $size
 * @property string $manipulations
 * @property string $custom_properties
 * @property string $generated_conversions
 * @property string $responsive_images
 * @property int|null $order_column
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCollectionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereConversionsDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCustomProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereGeneratedConversions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereManipulations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereResponsiveImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereUuid($value)
 * @mixin \Eloquent
 */
	class Media extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $uuid
 * @property int $buyer_id
 * @property numeric $total_amount
 * @property array<array-key, mixed>|null $shipping_address
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $placed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $buyer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @method static \Database\Factories\OrderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBuyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePlacedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUuid($value)
 * @mixin \Eloquent
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_id
 * @property int|null $product_id
 * @property int|null $seller_id
 * @property string|null $sku
 * @property int $quantity
 * @property string $unit_price
 * @property string $subtotal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read UserProduct|null $product
 * @method static \Database\Factories\OrderItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class OrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $phone
 * @property string $otp
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Otp whereUpdatedAt($value)
 */
	class Otp extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string|null $caption
 * @property string $type
 * @property array<array-key, mixed>|null $media_metadata
 * @property string $status
 * @property string $visibility
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Like> $likes
 * @property-read int|null $likes_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\View> $views
 * @property-read int|null $views_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post active()
 * @method static \Database\Factories\PostFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereMediaMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post withoutTrashed()
 * @mixin \Eloquent
 */
	class Post extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string $title
 * @property string|null $slug
 * @property string|null $description
 * @property numeric $price
 * @property int $stock
 * @property array<array-key, mixed>|null $images
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $seller
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 * @mixin \Eloquent
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string|null $video_url
 * @property string|null $cover_url
 * @property string|null $description
 * @property int|null $duration_seconds
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Like> $likes
 * @property-read int|null $likes_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel active()
 * @method static \Database\Factories\ReelFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereCoverUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereDurationSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereVideoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel withoutTrashed()
 * @mixin \Eloquent
 */
	class Reel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $reported_by
 * @property string $reportable_type
 * @property int $reportable_id
 * @property string|null $reason
 * @property string|null $details
 * @property string $status
 * @property int|null $resolved_by
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $reportable
 * @property-read \App\Models\User $reporter
 * @property-read \App\Models\User|null $resolver
 * @method static \Database\Factories\ReportFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReportableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReportableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReportedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereResolvedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Report extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $media_url
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $is_archived
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story active()
 * @method static \Database\Factories\StoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereMediaUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story withoutTrashed()
 * @mixin \Eloquent
 */
	class Story extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property-read \Illuminate\Support\Collection|\Spatie\Permission\Models\Role[] $roles
 * @method bool hasRole(string|array|\Spatie\Permission\Contracts\Role $roles)
 * @property int $id
 * @property string $uuid
 * @property string|null $username
 * @property string|null $occupation
 * @property string $name
 * @property string $email
 * @property int $is_seller
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property string|null $gender
 * @property string $language_preference
 * @property int $two_factor_enabled
 * @property string $messaging_privacy
 * @property int $is_online
 * @property string|null $last_seen_at
 * @property string|null $device_tokens
 * @property int $reputation_score
 * @property string|null $email_notification_preferences
 * @property string $account_privacy
 * @property string $password
 * @property string|null $profile_photo_path
 * @property string|null $bio
 * @property string $status
 * @property int $is_blocked
 * @property int $is_verified
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property int $followers_count
 * @property int $following_count
 * @property string|null $settings
 * @property-read int|null $posts_count
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $is_employer
 * @property array<array-key, mixed>|null $interests
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserChatMessage> $chatMessages
 * @property-read int|null $chat_messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserChatParticipant> $chatParticipants
 * @property-read int|null $chat_participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserChat> $chats
 * @property-read int|null $chats_count
 * @property-read int $notification_unread_count
 * @property-read mixed $profile_photo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reel> $reels
 * @property-read int|null $reels_count
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAccountPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeviceTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailNotificationPreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFollowersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFollowingCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereInterests($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsEmployer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsSeller($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLanguagePreference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMessagingPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePostsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReputationScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $followers
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $following
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 */
	class User extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string $type
 * @property string|null $caption
 * @property int $like_count
 * @property int $comment_count
 * @property string $status
 * @property string $visibility
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read mixed $images
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Like> $likes
 * @property-read int|null $likes_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost ofType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereCommentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereLikeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost withoutTrashed()
 * @mixin \Eloquent
 * @property int $view_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\View> $views
 * @property-read int|null $views_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost visiblePosts($user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost visibleReels($user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereViewCount($value)
 */
	class UserPost extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $type
 * @property string|null $caption
 * @property array<array-key, mixed>|null $meta
 * @property int $like_count
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserStoryHighlight> $highlights
 * @property-read int|null $highlights_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserStoryLike> $likes
 * @property-read int|null $likes_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\View> $views
 * @property-read int|null $views_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereLikeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereUserId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserStoryLike> $likes_old
 * @property-read int|null $likes_old_count
 */
	class UserStory extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property int|null $cover_media_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserStory> $stories
 * @property-read int|null $stories_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight whereCoverMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight whereUserId($value)
 */
	class UserStoryHighlight extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $story_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Story $story
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike whereStoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike whereUserId($value)
 * @mixin \Eloquent
 */
	class UserStoryLike extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $viewable_type
 * @property int $viewable_id
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read Model|\Eloquent $viewable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereViewableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereViewableType($value)
 * @mixin \Eloquent
 */
	class View extends \Eloquent {}
}

