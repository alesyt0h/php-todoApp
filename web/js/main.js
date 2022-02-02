// Relative Scroll
(function(){
    const main = document.querySelector('main');
    
    if(main.offsetHeight < main.scrollHeight - 80){
        main.classList.add('overflow-y-scroll');
    }
})();

// Hides appMsg correctly
const appMsgHidder = (el) => {

    el.parentElement.style.height = el.parentElement.offsetHeight + 'px';
    el.parentElement.style.width = (el.parentElement.offsetWidth + 1) + 'px';
    el.parentElement.classList.add('opacity-0', 'invisible');
    
    setTimeout(() => {
        el.parentElement.style = 'padding: 0';
        el.parentElement.style.height = '0px';
        el.parentElement.style.width = '0px';
        el.parentElement.innerHTML = '';

    }, 600);

}