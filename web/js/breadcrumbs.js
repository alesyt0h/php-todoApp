(function(){

    const currentUrl = window.location.href;
    const baseUrl = window.location.origin;

    const rootPath = baseUrl + webRoot;

    if(currentUrl === rootPath || currentUrl === rootPath + '/'){

        const dashboard = document.querySelector('#dashboard');
        dashboard.classList.add('bg-[#06080E]', 'text-white');
        dashboard.classList.remove('text-gray-300', 'hover:bg-gray-700', 'hover:text-white');

    } else if(currentUrl.includes('todo/new')) {

        const newTodo = document.querySelector('#new');
        newTodo.classList.add('bg-[#06080E]', 'text-white');
        newTodo.classList.remove('text-gray-300', 'hover:bg-gray-700', 'hover:text-white');

    } else if(currentUrl.includes('todo/list')) {

        const listTodo = document.querySelector('#list');
        listTodo.classList.add('bg-[#06080E]', 'text-white');
        listTodo.classList.remove('text-gray-300', 'hover:bg-gray-700', 'hover:text-white');
    };

})();