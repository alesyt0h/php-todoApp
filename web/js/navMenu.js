(function(){

    const currentUrl = window.location.href;
    const baseUrl = window.location.origin;

    const rootPath = baseUrl + webRoot;

    if(currentUrl === rootPath || currentUrl === rootPath + '/' || currentUrl.includes(rootPath + '/?')){

        const dashboard = document.querySelector('#dashboard');
        dashboard.classList.add('bg-gray-900', 'text-white');
        dashboard.classList.remove('text-gray-300', 'hover:bg-gray-700', 'hover:text-white');

        const dashboardMobile = document.querySelector('#dashboard-mobile');
        dashboardMobile.classList.add('bg-gray-900', 'text-white');
        dashboardMobile.classList.remove('text-gray-300', 'hover:bg-gray-700', 'hover:text-white');

    } else if(currentUrl.includes('todo/new') || currentUrl.match(/todo$/)) {

        const newTodo = document.querySelector('#new');
        newTodo.classList.add('bg-gray-900', 'text-white');
        newTodo.classList.remove('text-gray-300', 'hover:bg-gray-700', 'hover:text-white');

        const newMobileTodo = document.querySelector('#new-mobile');
        newMobileTodo.classList.add('bg-gray-900', 'text-white');
        newMobileTodo.classList.remove('text-gray-300', 'hover:bg-gray-700', 'hover:text-white');

    } else if(currentUrl.includes('todo/list')) {

        const listTodo = document.querySelector('#list');
        listTodo.classList.add('bg-gray-900', 'text-white');
        listTodo.classList.remove('text-gray-300', 'hover:bg-gray-700', 'hover:text-white');

        const listMobileTodo = document.querySelector('#list-mobile');
        listMobileTodo.classList.add('bg-gray-900', 'text-white');
        listMobileTodo.classList.remove('text-gray-300', 'hover:bg-gray-700', 'hover:text-white');
    };

})();

const mobileMenuOpener = () => {

    mobileMenu = document.querySelector('#mobile-menu');
    openMobile = document.querySelector('#open-mobile');
    closeMobile = document.querySelector('#close-mobile');

    if(!closeMobile.classList.contains('hidden')){
        mobileMenu.classList.remove('animate-fade');
        mobileMenu.classList.add('animate-fade-out');
        mobileMenu.classList.toggle('opacity-0');

        setTimeout(() => {
            mobileMenu.setAttribute('style', 'display: none;');
        }, 400);
    } else {
        mobileMenu.classList.remove('animate-fade-out');
        mobileMenu.classList.add('animate-fade');
        mobileMenu.classList.toggle('opacity-0');
        mobileMenu.removeAttribute('style');
    }
    
    openMobile.classList.toggle('hidden');
    closeMobile.classList.toggle('hidden');

}