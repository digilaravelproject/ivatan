                    <div>
                        <label class="block text-sm font-medium mb-1">Category</label>
                        <select name="category" required class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Title</label>
                        <input type="text" name="title" required maxlength="255" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Notification title">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Message</label>
                        <textarea name="message" required rows="3" maxlength="5000" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Notification message body"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Action URL <span class="text-gray-400">(optional)</span></label>
                        <input type="url" name="action_url" maxlength="500" class="w-full rounded-lg border-gray-300 text-sm" placeholder="https://...">
                    </div>
