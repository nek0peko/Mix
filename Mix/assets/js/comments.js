(function () {
    'use strict';
    if (window.MixComments) return;
    var pending = new WeakSet();
    function section() { return document.querySelector('main .mix-comment-section'); }
    function notice(text, color) {
        var message = document.createElement('span');
        message.textContent = text;
        if (window.ks && ks.notice) ks.notice(message.innerHTML, { color: color, time: 1800 });
    }
    function reply(cid, coid) {
        var root = section();
        var comment = document.getElementById(cid);
        var response = root && root.querySelector('.reply');
        if (!response || !comment || !root.contains(comment)) return false;
        var form = response.querySelector('form');
        if (!form || !form.querySelector('[name="text"]')) return false;
        var parent = form.querySelector('[name="parent"]');
        if (!parent) {
            parent = document.createElement('input');
            parent.type = 'hidden'; parent.name = 'parent'; parent.id = 'comment-parent';
            form.appendChild(parent);
        }
        parent.value = coid;
        if (!root.querySelector('#comment-form-place-holder')) {
            var holder = document.createElement('div');
            holder.id = 'comment-form-place-holder'; holder.hidden = true;
            response.before(holder);
        }
        comment.appendChild(response);
        response.style.display = '';
        var cancel = response.querySelector('#cancel-comment-reply-link');
        if (cancel) cancel.style.display = '';
        form.querySelector('[name="text"]').focus();
        return false;
    }
    function cancelReply() {
        var root = section(), response = root && root.querySelector('.reply');
        if (!response) return false;
        var parent = response.querySelector('[name="parent"]');
        if (parent) parent.remove();
        var holder = root.querySelector('#comment-form-place-holder');
        if (holder) { holder.before(response); holder.remove(); }
        response.style.display = 'none';
        return false;
    }
    function init() {
        // Use one implementation for initial loads, Pjax and refreshed replies.
        window.TypechoComment = Object.assign(window.TypechoComment || {}, { reply: reply, cancelReply: cancelReply });
    }
    function submit(form) {
        var root = section();
        if (!root || pending.has(form) || !window.ks || !ks.ajax) return;
        var field = form.querySelector('[name="text"]');
        if (!field) return;
        var submitted = field.value;
        var data = {};
        new FormData(form).forEach(function (value, key) { data[key] = value; });
        var isReply = form.classList.contains('reply_form');
        var button = form.querySelector('[type="submit"]');
        pending.add(form);
        if (button) button.disabled = true;
        form.setAttribute('aria-busy', 'true');
        notice('正在提交，请稍等哈', 'yellow');
        function release() {
            pending.delete(form);
            form.removeAttribute('aria-busy');
            if (button) button.disabled = false;
        }
        try {
            ks.ajax({
                url: form.action, method: 'POST', data: data,
                success: function (res) {
                    try {
                        // A response from an earlier page must not overwrite the current page.
                        if (!root.isConnected || section() !== root) return;
                        var response = new DOMParser().parseFromString(res.responseText, 'text/html');
                        var updated = response.querySelector('.mix-comment-section');
                        if (!updated || (updated.dataset.cid && updated.dataset.cid !== root.dataset.cid)) {
                            var error = response.querySelector('.container');
                            throw new Error(error ? error.textContent.trim() : '评论提交失败，请稍后重试');
                        }
                        var changed = updated.innerHTML !== root.innerHTML;
                        var draft = field.value === submitted ? '' : field.value;
                        var identity = {};
                        form.querySelectorAll('[name="author"], [name="mail"], [name="url"]').forEach(function (input) { identity[input.name] = input.value; });
                        root.innerHTML = updated.innerHTML;
                        if (updated.dataset.commentCount !== undefined) root.dataset.commentCount = updated.dataset.commentCount;
                        field.value = draft;
                        var currentForm = isReply ? root.querySelector('.reply_form') : document.getElementById('comment-form');
                        if (currentForm) {
                            currentForm.querySelector('[name="text"]').value = draft;
                            currentForm.querySelectorAll('[name="author"], [name="mail"], [name="url"]').forEach(function (input) {
                                if (identity[input.name] !== undefined) input.value = identity[input.name];
                            });
                            if (isReply && draft) {
                                if (data.parent && document.getElementById('comment-' + data.parent)) reply('comment-' + data.parent, data.parent);
                                else currentForm.closest('.reply').style.display = '';
                            }
                        }
                        init();
                        document.dispatchEvent(new CustomEvent('mix:comments-updated', { detail: { cid: root.dataset.cid, count: Number(root.dataset.commentCount) } }));
                        notice(changed ? '评论成功了 (〃\'▽\'〃)' : '请等待审核哦 φ(>ω<*)', 'green');
                    } catch (error) {
                        notice(error.message || '评论提交失败，请稍后重试', 'red');
                    } finally { release(); }
                },
                failed: function () {
                    release();
                    if (root.isConnected) notice('评论提交失败，请稍后重试', 'red');
                }
            });
        } catch (error) { release(); notice('评论提交失败，请稍后重试', 'red'); }
    }
    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.matches('#comment-form, .reply_form')) return;
        if (!window.ks || !ks.ajax) return;
        event.preventDefault();
        // Stop Pjax and legacy form handlers before either sends another POST.
        event.stopPropagation();
        submit(form);
    }, true);
    document.addEventListener('click', function (event) {
        if (event.defaultPrevented) return;
        var link = event.target.closest('.comment_reply a, #cancel-comment-reply-link');
        if (!link) return;
        if (link.id === 'cancel-comment-reply-link') { event.preventDefault(); cancelReply(); return; }
        var match = link.href.match(/[?&]replyTo=(\d+)/);
        if (match) { event.preventDefault(); reply('comment-' + match[1], match[1]); }
    });
    document.addEventListener('pjax:complete', init);
    if (window.jQuery) window.jQuery(document).on('pjax:complete.mixComments', init);
    window.MixComments = { init: init };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
