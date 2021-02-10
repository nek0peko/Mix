

(() => {
    window.getElementTop = function getElementTop(element) {
        let actualTop = element.offsetTop
        let current = element.offsetParent
        while (current !== null) {
            actualTop += current.offsetTop
            current = current.offsetParent
        }
        return actualTop
    }

    window.scrollSmoothTo = function scrollSmoothTo(position) {
        if (!window.requestAnimationFrame) {
            window.requestAnimationFrame = function (callback, element) {
                return setTimeout(callback, 17)
            }
        }
        // 当前滚动高度
        let scrollTop = document.documentElement.scrollTop || document.body.scrollTop
        // 滚动step方法
        const step = function () {
            // 距离目标滚动距离
            let distance = position - scrollTop
            // 目标滚动位置
            scrollTop = scrollTop + distance / 5
            if (Math.abs(distance) < 1) {
                window.scrollTo(0, position)
            } else {
                window.scrollTo(0, scrollTop)
                requestAnimationFrame(step)
            }
        }
        step()
    }
    window.comment_init = function () {
const commentsReply = document.querySelectorAll('.comment_reply a')
const replyForm = document.querySelector('.reply')
const isComment = document.querySelector('.post-form.is-comment')
for (let el of commentsReply) {
    el.addEventListener('click', e => {
        // 给恢复按钮绑定事件 获取parent-id
        const href = e.target.getAttribute('href')
        window.parentId = href.match(/replyTo=(\d+)/)[1]
        // 弹出回复框
        replyForm.removeAttribute('style')
        if (isComment.classList.contains('active')) isComment.classList.remove('active')
        setTimeout(() => {
            document.getElementById('cancel-comment-reply-link').addEventListener('click', () => {
                replyForm.style.display = 'none'
            })
        })
    })
}
}

window.domParse = new DOMParser()
window.parser = dom => {
return window.domParse.parseFromString(dom, 'text/html')
}
})()
