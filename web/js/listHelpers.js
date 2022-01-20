const printBadge = (status) => {
    switch (status) {
        case 'Pending':
            badge = ['bg-red-100', 'text-red-800'];
            break;
        case 'In Process':
            badge = ['bg-yellow-100','text-yellow-800'];
            break;
        case 'Completed':
            badge = ['bg-green-100','text-green-800'];
            break;
    }

    return badge;
}

const timeAgo = (dateTime) => {

    if(typeof dateTime !== 'string') return;

    const DATE_UNITS = {
        year: 31104000,
        month: 2592000,
        day: 86400,
        hour: 3600,
        minute: 60,
        second: 1
    }

    const rtf = new Intl.RelativeTimeFormat('en', { style: 'long'});

    const now = Date.now();
    const ago = new Date(dateTime).getTime();

    const secondsDiff = (now - ago) / 1000;

    for(const [unit, secondsInUnit] of Object.entries(DATE_UNITS)){
        if(secondsDiff >= secondsInUnit || unit === 'second'){
            const value = Math.floor(secondsDiff / secondsInUnit) * -1;
            return (unit === 'second' && value > -10) ? 'Just now' : rtf.format(value, unit);
        }
    }
}

const init = () => {
    const tbody = document.querySelector('tbody');
    const trElem = document.querySelectorAll('tr')[1];
    
    trElem.remove();

    todos.forEach(todo => {
        const tr = trElem.cloneNode(true);
    
        const badge = printBadge(todo.status)
        const completed = todo.status === 'Completed';
        const completedClasses = (completed) ? ['line-through','text-gray-400'] : 'text-gray-900';
    
        const title = tr.querySelector('#title');
        title.innerText = todo.title;
        title.classList.add(...completedClasses);
    
        const input = tr.querySelector('input');
        input.checked = completed;
        input.id = todo.id;
    
        const created = tr.querySelector('#created');
        created.after((completed) ? timeAgo(todo.completedAt) : timeAgo(todo.createdAt));
        created.parentElement.title = (completed) ? 'Created ' + timeAgo(todo.createdAt) : '';
        created.innerText = (completed) ? 'Completed ' : '';
    
        const status = tr.querySelector('#status');
        status.innerText = todo.status;
        status.classList.add(...badge);
    
        tr.querySelector('.edit').href += todo.id; 
        tr.querySelector('.trash').href += todo.id;
    
        tbody.appendChild(tr);
    });
}

const removeOverflowEffect = () => {

    document.querySelectorAll('.title-wrap').forEach((el) => {
        if (el.offsetWidth === el.scrollWidth) {
            el.classList.remove(
                'hover:absolute',
                'hover:top-[-12px]',
                'hover:rounded-[1px]',
                'hover:shadow-[0_0_4px_0_black]',
                'hover:bg-white',
                'hover:z-50',
                'hover:whitespace-normal'
            );
        }
    });
}