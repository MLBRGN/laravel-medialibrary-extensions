// document.addEventListener("DOMContentLoaded", () => {
//     document.querySelectorAll('[data-mle-document-link]').forEach(link => {
//         console.log('add listener to click on link', link);
//         link.addEventListener('click', (e) => {
//             e.preventDefault();
//             e.stopPropagation();   // prevent bubbling to container
//             e.stopImmediatePropagation();  // stop other handlers on document
//             console.log('clicked', link);
//             // optionally also prevent bootstrap from hijacking
//             // e.preventDefault(); // only if you don't want navigation
//         });
//     });
// });
