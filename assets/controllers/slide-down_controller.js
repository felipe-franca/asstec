import { up, down, toggle  } from 'slide-element'

let aElement = document.getElementsByClassName('alert');
aElement.addEventListener('change',
(e) => {
    down(aElement);
})
