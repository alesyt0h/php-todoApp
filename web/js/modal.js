(function(){
    const modal = document.querySelector('#modal');
    const modalBg = document.querySelector('#modal-bg');
    const modalBgAlt = document.querySelector('#modal-bg-alt');
    const cancel = document.querySelector('#cancel');
    
    cancel.addEventListener('click', () => {
        modal.classList.add('animate-smooth-out', 'opacity-0');
        modalBg.classList.add('animate-fade-out', 'opacity-0');
        modalBgAlt.classList.add('animate-fade-out', 'opacity-0');
    
        setTimeout(() => {
            modal.remove();
            modalBg.remove();
            modalBgAlt.remove();
            
            const url = window.location.href.replace(/\?(.*)/, '');
            window.location.href = url;
        }, 500);
    });
})();
