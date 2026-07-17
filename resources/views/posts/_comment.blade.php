{{--
    递归渲染单条评论及其子回复
    接收参数：
    - $comment  当前评论实例
    - $post     当前文章实例
    - $depth    当前层级深度（0 为顶层评论）
    - $replyTo  当前评论是回复给哪位用户（null 表示顶层评论）
--}}
<li class="comment-item {{ $depth > 0 ? 'reply-item' : '' }} {{ $depth > 1 ? 'nested-reply-item' : '' }}" id="comment-{{ $comment->id }}">
    <div class="comment-avatar">
        <x-image-placeholder :src="$comment->user->avatar" alt="{{ $comment->user->name }}" type="avatar" class="comment-avatar-img" />
    </div>
    <div class="comment-body">
        <div class="comment-header">
            <strong>{{ $comment->user->name ?? '匿名访客' }}</strong>
            <span>{{ $comment->created_at->format('Y-m-d H:i') }}</span>
            <span class="comment-meta" title="{{ $comment->user_agent }}">
                <i class="bi bi-geo-alt"></i> {{ $comment->city }}
                <i class="bi bi-{{ $comment->device_type === '手机' ? 'phone' : 'laptop' }}"></i> {{ $comment->device_type }}
            </span>
        </div>
        <div class="comment-text">
            @if ($replyTo)
                <span class="reply-to">{{ $replyTo }}</span>
            @endif
            {!! $comment->parseContent() !!}
        </div>

        @auth
            @php
                $canManage = Auth::id() === $comment->user_id && $comment->nestedReplies->isEmpty();
                $showReplyForm = old('parent_id') == $comment->id;
                $showEditForm = old('edit_comment_id') == $comment->id;
            @endphp

            <div class="comment-actions"
                 id="comment-actions-{{ $comment->id }}"
                 style="display: {{ $showReplyForm || $showEditForm ? 'none' : 'flex' }}">
                <button type="button"
                        class="reply-toggle"
                        data-target="reply-form-{{ $comment->id }}"
                        data-actions="comment-actions-{{ $comment->id }}">
                    <i class="bi bi-reply"></i> 回复
                </button>

                @if ($canManage)
                    <button type="button"
                            class="edit-toggle"
                            data-target="edit-form-{{ $comment->id }}"
                            data-actions="comment-actions-{{ $comment->id }}">
                        <i class="bi bi-pencil"></i> 编辑
                    </button>

                    <form action="{{ route('posts.comments.destroy', [$post->slug, $comment]) }}"
                          method="post"
                          class="comment-delete-form"
                          onsubmit="return confirm('确定要删除这条评论吗？');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-comment-btn">
                            <i class="bi bi-trash"></i> 删除
                        </button>
                    </form>
                @endif
            </div>

            <form id="reply-form-{{ $comment->id }}"
                  action="{{ route('posts.comments.store', $post->slug) }}"
                  method="post"
                  class="comment-form reply-form"
                  style="display: {{ $showReplyForm ? 'block' : 'none' }};">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <x-comment-toolbar textarea-id="reply-content-{{ $comment->id }}" />
                <textarea id="reply-content-{{ $comment->id }}" name="content" rows="3" placeholder="回复 {{ $comment->user->name ?? '匿名访客' }}..." required maxlength="1000">{{ $showReplyForm ? old('content') : '' }}</textarea>
                @error('content')
                    @if ($showReplyForm)
                        <p class="error-text">{{ $message }}</p>
                    @endif
                @enderror
                <div class="reply-actions">
                    <button type="button" class="cancel-reply" data-target="reply-form-{{ $comment->id }}" data-actions="comment-actions-{{ $comment->id }}">取消</button>
                    <button type="submit" class="submit-btn"><i class="bi bi-send"></i> 发表回复</button>
                </div>
            </form>

            @if ($canManage)
                <form id="edit-form-{{ $comment->id }}"
                      action="{{ route('posts.comments.update', [$post->slug, $comment]) }}"
                      method="post"
                      class="comment-form edit-form"
                      style="display: {{ $showEditForm ? 'block' : 'none' }};">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="edit_comment_id" value="{{ $comment->id }}">
                    <x-comment-toolbar textarea-id="edit-content-{{ $comment->id }}" />
                    <textarea id="edit-content-{{ $comment->id }}" name="content" rows="3" required maxlength="1000">{{ $showEditForm ? old('content') : $comment->content }}</textarea>
                    @error('content')
                        @if ($showEditForm)
                            <p class="error-text">{{ $message }}</p>
                        @endif
                    @enderror
                    <div class="reply-actions">
                        <button type="button" class="cancel-edit" data-target="edit-form-{{ $comment->id }}" data-actions="comment-actions-{{ $comment->id }}">取消</button>
                        <button type="submit" class="submit-btn"><i class="bi bi-check-lg"></i> 保存修改</button>
                    </div>
                </form>
            @endif
        @endauth

        @if ($comment->nestedReplies->count())
            <ul class="comment-replies">
                @foreach ($comment->nestedReplies as $reply)
                    @include('posts._comment', [
                        'comment' => $reply,
                        'post' => $post,
                        'depth' => $depth + 1,
                        'replyTo' => $comment->user->name ?? '匿名访客',
                    ])
                @endforeach
            </ul>
        @endif
    </div>
</li>
