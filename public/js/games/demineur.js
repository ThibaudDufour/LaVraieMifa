
$(document).ready(function() {

    url = (window.location.href).split('=')

    difficulte = url[1]
    
    if(difficulte == "1"){
        var nbmine = 10;
        var lar = 9;
        var lon = 9;
    } else if(difficulte == "2"){
        var nbmine = 20;
    } else if(difficulte == "3"){
        var nbmine = 30;
    }
    var countMine = 0;
        $('.ok').click(function() {
            if($(this).hasClass('ok')) {
                if(!$(this).hasClass('drapo')) {
                    $(this).addClass('clic');
                    if($(this).hasClass('mine')) {
                        $(this).attr('src', '/images/demineur/mine.png')
                        $('.ok').each(function(){
                            $(this).removeClass('ok');
                        });
                        alert('Game Over')  
                    } else {
                        $(this).attr('src', '/images/demineur/zero.png')
                        $(this).removeClass('ok');
                    }
                    if(firstClick == false){
                        placeMine();
                        placeNumber();
                        revealClose();
                        firstClick = true;
                    }
                } 
            }    
        });
    
        $('.ok').mousedown(function(event) {
            switch (event.which) {
                case 2:
                    if(!$(this).hasClass('drapo') && !$(this).hasClass('clic') && $(this).hasClass('ok')){
                        $(this).addClass('drapo');
                        $(this).attr('src', '/images/demineur/drapo.png')
                    } else if(!$(this).hasClass('clic')) {
                        $(this).removeClass('drapo');
                        $(this).attr('src', '/images/demineur/case.png')
                    }
                    break;
                default:
                    break;
            }
        });
    
        var firstClick = false;
        var classes = ["","","","","","","","","","","","","","","","","","mine"];
    
        //A faire dans un FOR et incrémenter jusque 10 mines pour limiter le nb de mines
        function placeMine(){
            while(nbmine > 0)
            {
                $('.ok').each(function(){
                    if(!$(this).hasClass('mine') && !$(this).hasClass('clic') && nbmine > 0) {
                        let isMine = classes[~~(Math.random()*classes.length)]
                        if(isMine == "mine")
                        {
                            $(this).addClass(isMine);
                            nbmine--;
                        }            
                    }
                });  
            }
        }
    
        function placeNumber()
        {
            let xy, x, y, case1, case2, case3, case4, case5, case6, case7, case8;
            $('.ok').each(function(){
                xy = this.getAttribute("class").split(' ')
                x = xy[0] // vérifier x-1/y-1, x/y-1, x+1/y-1
                y = xy[1] //          x-1/y,        , x+1/y
                          //          x-1/y+1, x/y+1, x+1/y+1
    
                case1 = (x-1) + ' ' + (y-1)
            /*    case2 = (x) + ' ' + (y-1)
                case3 = (x+1) + ' ' + (y-1)
                case4 = (x-1) + ' ' + (y)
                case5 = (x+1) + ' ' + (y)
                case6 = (x-1) + ' ' + (y+1)
                case7 = (x) + ' ' + (y+1)
                case8 = (x+1) + ' ' + (y+1)*/
    
                voisins = [case1]
                //, case2, case3, case4, case5, case6, case7, case8
    
                voisins.forEach(function(currentCase) {
                    coor = currentCase.split(' ')// Tableau des coordonnées d'un voisin
                    if((coor[0] < 0 || coor[0] > lon-1) || (coor[1] < 0 || coor[1] > lon-1)){// Si les coordonnées dépassent les limites du jeu, case invalide
                        coor[0] = "Case invalide"
                        coor[1] = "Case invalide"
                    } else {
                        //Ici, on a les coordonnées des voisins
                        //Récupérer les classes du voisin
                        //Si 'mine' est présent, on incrémente countMine
                        /*switch (countMine) {
                            case 0:
                                //Donner la classe zero à la case courante
                                $(this).addClass('zero');
                                break;
                            case 1:
                                $(this).addClass('un');
                                break;
                            case 2:
                                $(this).addClass('deux');
                                break;
                            case 3:
                                $(this).addClass('trois');
                                break;
                            case 4:
                                $(this).addClass('quatre');
                                break;
                            case 5:
                                $(this).addClass('cinq');
                                break;
                            case 6:
                                $(this).addClass('six');
                                break;
                            case 7:
                                $(this).addClass('sept');
                                break;
                            case 8:
                                $(this).addClass('huit');
                                break;
                        }*/
                    }
                    
                });
            });
        }
    
        function revealClose()
        {
            //réveler tous les 0 adjacents
        }
    });
    
    