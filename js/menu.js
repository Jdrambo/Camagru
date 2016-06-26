window.onload = (function(){
    var menu_state = 0;
    var btn_menu = document.getElementById('img-menu');
    if (btn_menu)
        btn_menu.addEventListener("click", function(){ openMenu();}, false);
    
    function openMenu(){
        if (menu_state == 0){
            displayElem('menu-list');
        }
        else {
            hideElem('menu-list');
        }
    }
    
    function displayElem(id){
        var elem = document.getElementById(id);
        elem.style.display = "inline-block";
        menu_state = 1;
    }
    
    function hideElem(id){
        var elem = document.getElementById(id);
        elem.style.display = "none";
        menu_state = 0;
    }
})();