(function(){
    const main = document.querySelector('main');
    
    if(main.offsetHeight < main.scrollHeight - 40){
        main.classList.add('overflow-y-scroll');
    }
})();