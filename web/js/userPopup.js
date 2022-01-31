(function(){

    const profileOpener = document.querySelector('#profile-opener');
    const menuDiv = profileOpener.nextElementSibling;

    profileOpener.addEventListener('click', (e) => {
        menuDiv.classList.toggle('opacity-0');
        menuDiv.classList.toggle('invisible');
    });

    document.addEventListener('keyup', (e) => {
        (e.code === 'Escape') ? menuDiv.classList.add('opacity-0', 'invisible') : null;
    });

    document.addEventListener('click', (e) => {
        if(e.target.parentElement !== menuDiv && e.target.parentElement.parentElement !== profileOpener ){
            menuDiv.classList.add('opacity-0', 'invisible');
        };
    });
    
})();