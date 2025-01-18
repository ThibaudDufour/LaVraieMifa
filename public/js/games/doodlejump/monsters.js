function spawnMonster() {
    var monsterChances = 15;

    if (Math.round(Math.random() * monsterChances) === 0) {
        return "smallRed";
    }
    return 0;
}

var monster = {
    0: "smallRed",
    1: "araignee",
    2: "jeanclaude",
    3: "morve",
    4: "mouche",
    5: "tetedecon",
};

var smallRed = new function() {
    this.img = new Image();
    this.img.src = "/images/doodlejump/Monsters/" + monster[Math.floor(Math.random() * 6)] + ".png";
    this.xDif = 10;
    this.yDif = -30;
    this.width = 69;
    this.height = 60;

    this.draw = function(blockX, blockY) {
        ctx.drawImage(this.img, blockX + this.xDif, blockY + this.yDif, this.width, this.height);
    }
}

var monsterFunctions = {
    "smallRed": smallRed
}