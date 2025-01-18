var SoundManager = (function() {

  var playedsound = ""
  playedsound = "true" //sessionStorage.getItem('soundPacman');

  var sounds = {
    pellet1: null,
    pellet2: null,
    powerpellet: null,
    eatghost: null,
    pacman_dies: null,
    intro: null,
    eatfruit: null
  };
  

  for (var i in sounds) {
    var snd = new Audio("/sfx/pacman/" + i + ".ogg");
    sounds[i] = snd;
  }
  
  return {
    notify: function (event) {
      console.log(playedsound)
      if (event.name == EVENT_PELLET_EATEN && playedsound == "true") {
        sounds[event.pacman.getEatenPelletSound()].play();
      }
      else if (event.name == EVENT_POWER_PELLET_EATEN && playedsound == "true") {
        sounds.powerpellet.play();
      }
      else if (event.name == EVENT_GHOST_EATEN && playedsound == "true") {
        sounds.eatghost.play();
      }
      else if (event.name == EVENT_PACMAN_DIES_ANIMATION_STARTED && playedsound == "true") {
        sounds.pacman_dies.play();
      }
      else if (event.name == EVENT_PLAYSCENE_READY && playedsound == "true") {
        sounds.intro.play();
      }
      else if (event.name == EVENT_CHERRY_EATEN && playedsound == "true") {
        sounds.eatfruit.play();
      }
    }
  };
})();
