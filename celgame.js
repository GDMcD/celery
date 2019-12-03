var cwidth = 780;
var cheight = 600;

var jumpSpeed = 5;
var fallingConstant = 0.25;
var currentScene = 0;
var sunWidth;
var sunBounce = 0;
var sunRot = 0;
var pressedKeys = [];
var canJump = true;
var upCount = 0;
var score = 0;

function drawLeaf(x, y, angle, leafColor) {
    push();
    translate(x, y);
    rotate(angle);
    scale(1.5);
    noStroke();
    fill(leafColor);
    ellipse(0, 0, 15, 65);
    rotate(-5 * PI / 36);
    ellipse(-13, 16, 15, 25);
    ellipse(-7, 3, 15, 30);
    rotate(PI / 18);
    ellipse(0, -11, 15, 25);
    rotate(PI / 6);
    ellipse(0, -11, 15, 25);
    rotate(PI / 18);
    ellipse(13, 16, 15, 25);
    ellipse(7, 3, 15, 30);
    rotate(-5 * PI / 36);
    fill(218, 227, 146);
    triangle(-1, 32, 1, 32, 0, -20);
    pop();
  }
function drawSun(x, y, diameter, theta) {
    push();
    translate(x, y);
    scale(diameter);
    rotate(theta);
    fill(255, 221, 0);
    stroke(255, 221, 0);
    strokeWeight(0.05);
    for (var i = 0; i < 12; i++) {
      rotate(PI / 6);
      triangle(0, 1.0, 0.6, 0, -0.6, 0);
    }
    fill(255, 246, 71);
    ellipse(0, 0, 1.4, 1.4);
    pop();
  }

class Celery {
    constructor(x, y, scale) {
      this.x = x;
      this.y = y;
      this.speed = 0;
      this.xspeed = 0;
      this.scale = scale;
    }
    display() {
      push();
      translate(this.x, this.y);
      rotate(-7 * PI / 180);
      scale(this.scale);
      //left leg
      noFill();
      stroke(0);
      strokeWeight(10);
      curve(1, 90, 25, 90, 62, 158, 71, 237);
      line(62, 158, 79, 149);
      //left arm
      curve(24, -181, 29, 0, 86, 8, 124, -220);
      fill(0);
      ellipse(84, 8, 3, 3);
      //body
      noStroke();
      fill(218, 227, 146);
      rect(0, 0, 75, 200, 50);
      fill(181, 205, 85);
      rect(0, 0, 30, 195, 15);
      fill(209, 220, 126);
      rect(0, 0, 26, 190, 13);
      //right arm
      stroke(0);
      strokeWeight(10);
      noFill();
      curve(0, 47, -25, 0, -60, 50, 67, 172);
      fill(0);
      ellipse(-58, 50, 3, 3);
      //right leg
      noFill();
      curve(-33, -110, -25, 90, -84, 124, -184, -35);
      line(-84, 124, -96, 137);
      //face
      fill(0);
      ellipse(0, -25, 5, 5);
      ellipse(24, -25, 5, 5);
      strokeWeight(5);
      fill(110, 141, 72);
      ellipse(12, -2, 12, 10);
      //leaves
      noStroke();
      drawLeaf(-38, -108, -7 * PI / 45, color(113, 145, 72));
      drawLeaf(-23, -122, -17 * PI / 180, color(113, 145, 72));
      drawLeaf(12, -125, PI / 30, color(113, 145, 100));
      drawLeaf(45, -110, PI / 6, color(113, 157, 74));
      drawLeaf(0, -114, PI / 45, color(125, 157, 74));
      drawLeaf(-21, -107, -9 * PI / 180, color(144, 174, 78));
      drawLeaf(30, -112, 9 * PI / 180, color(144, 174, 78));
      //headband
      fill(209, 86, 44);
      rect(0, -50, 80, 30, 5);
      fill(248, 248, 222);
      rect(0, -50, 80, 10);
      fill(104, 150, 210);
      rect(0, -40, 80, 10, 5);
      rect(0, -42.5, 80, 5);
      pop();
      this.y -= this.speed;
      this.x = constrain(this.x, 50, cwidth - 50);
      this.x -= this.xspeed;
      this.y = constrain(this.y, 0, cheight - 90);
    }
    jump(direction) {
      if (direction === "u") {
        this.speed = jumpSpeed;
      } else if (direction === "r") {
        this.xspeed = -jumpSpeed + 1;
      } else if (direction === "l") {
        this.xspeed = jumpSpeed + 5;
      }
    }
    fall() {
      if (this.xspeed > 0) {
        this.xspeed -= 0.25;
      }
      if (this.xspeed < 0) {
        this.xspeed += 0.25;
      }
      this.speed -= fallingConstant;
    }
    collisionCheck(platform) {
      return (this.x - 72 * this.scale) < (platform.x + platform.w / 2) &&
        (this.x + 79 * this.scale) > (platform.x - platform.w / 2) &&
        (this.y + 156 * this.scale) < (platform.y + 10) &&
        (this.y + 156 * this.scale) > (platform.y - 10);
    }
    handleCollision(platform) {
      if (this.collisionCheck(platform)) {
        this.y = constrain(this.y, 0, platform.y - 156 * this.scale - 5);
        canJump = true;
        upCount = 0;
        if (this.speed < 0) {
          this.speed += fallingConstant; //counteract falling
        }
      }
    }
    waterCheck(drop) {
      return (this.x - 72 * this.scale) < drop.x &&
        (this.x + 79 * this.scale) > drop.x &&
        (this.y - 156 * this.scale) < drop.y &&
        (this.y + 156 * this.scale) > drop.y;
    }
    grabDrop(drop) {
      if (this.waterCheck(drop)) {
        drop.x = -400;
        score++;
      }
    }
  }
class Mountain {
    constructor(x, w, h, speed) {
      this.x = x;
      this.y = cheight - 70;
      this.w = w;
      this.h = h;
      this.color = random(130, 170);
      this.speed = speed || 0.15;
    }
    display() {
      push();
      translate(this.x, this.y);
      scale(this.w, this.h);
      noStroke();
      fill(this.color);
      triangle(0, 0, -1, 0, -0.5, -1);
      fill(0, 0, 0, 30);
      triangle(0, 0, -0.33, 0, -0.5, -1);
      fill(255, 255, 255);
      triangle(-0.5, -1, -0.33, -0.67, -0.6, -0.8);
      quad(-0.5, -1, -0.63, -0.75, -0.55, -0.65, -0.37, -0.75);
      pop();
    }
    scroll() {
      this.x -= this.speed;
      if (this.x < 0) {
        this.x = cwidth + 300;
      }
    }
  }
class Deciduous {
    constructor(x, h, speed) {
      this.x = x;
      this.y = cheight - 65;
      this.h = h;
      this.speed = speed || 0.25;
    }
    display() {
      push();
      translate(this.x, this.y);
      scale(this.h);
      translate(0, -0.08);
      noStroke();
      //light leaves
      fill(120, 171, 64);
      ellipse(-0.35, -0.38, 0.15, 0.15);
      ellipse(-0.31, -0.45, 0.15, 0.18);
      ellipse(-0.32, -0.54, 0.20, 0.15);
      ellipse(-0.29, -0.65, 0.16, 0.14);
      ellipse(-0.19, -0.69, 0.16, 0.14);
      ellipse(-0.09, -0.75, 0.11, 0.09);
      ellipse(-0.01, -0.72, 0.17, 0.17);
      ellipse(0.08, -0.69, 0.11, 0.10);
      ellipse(0.14, -0.72, 0.11, 0.10);
      ellipse(0.195, -0.68, 0.10, 0.10);
      ellipse(-0.03, -0.55, 0.59, -0.3);
      ellipse(-0.10, -0.71, 0.10, 0.10);
      ellipse(-0.25, -0.42, 0.10, 0.10);
      //dark leaves
      fill(101, 152, 54);
      ellipse(0.26, -0.60, 0.15, 0.14);
      ellipse(0.20, -0.51, 0.23, 0.23);
      ellipse(0.30, -0.45, 0.16, 0.14);
      ellipse(0.24, -0.39, 0.16, 0.16);
      ellipse(0.20, -0.34, 0.15, 0.15);
      ellipse(0.11, -0.42, 0.24, 0.27);
      ellipse(0.11, -0.42, 0.40, 0.29);
      ellipse(-0.16, -0.31, 0.18, 0.18);
      ellipse(-0.27, -0.34, 0.10, 0.08);
      ellipse(-0.09, -0.33, 0.18, 0.18);
      //dark brown branches
      fill(129, 73, 14);
      beginShape();
      vertex(-0.01, 0);
      vertex(-0.01, -0.2);
      vertex(-0.06, -0.45);
      vertex(-0.02, -0.45);
      vertex(0.03, -0.3);
      bezierVertex(0.039, -0.28, 0.065, -0.28, 0.08, -0.3);
      vertex(0.13, -0.4);
      vertex(0.18, -0.4);
      bezierVertex(0.08, -0.18, 0.09, -0.30, 0.10, 0);
      bezierVertex(0.10, 0.02, 0.12, 0.04, 0.15, 0.05);
      bezierVertex(0.153, 0.053, 0.155, 0.057, 0.15, 0.060);
      bezierVertex(0.14, 0.08, -0.02, 0.073, -0.03, 0.074);
      bezierVertex(-0.009, 0.05, -0.01, 0.02, -0.01, 0);
      endShape();
      beginShape();
      vertex(-0.21, -0.4);
      vertex(-0.16, -0.4);
      bezierVertex(-0.13, -0.36, 0.03, -0.26, 0.00, -0.14);
      endShape();
      //light brown branches
      fill(145, 84, 21);
      beginShape();
      vertex(0, 0);
      bezierVertex(0.00, -0.28, 0.00, -0.25, -0.01, -0.3);
      vertex(-0.05, -0.45);
      vertex(-0.08, -0.45);
      vertex(-0.04, -0.3);
      bezierVertex(-0.03, -0.26, -0.029, -0.26, -0.028, -0.18);
      bezierVertex(-0.03, -0.24, -0.12, -0.32, -0.20, -0.40);
      vertex(-0.23, -0.4);
      bezierVertex(-0.09, -0.26, -0.10, -0.21, -0.10, -0.20);
      bezierVertex(-0.10, 0.02, -0.12, 0.04, -0.15, 0.05);
      bezierVertex(-0.153, 0.053, -0.155, 0.057, -0.15, 0.060);
      bezierVertex(-0.145, 0.065, -0.07, 0.073, -0.03, 0.074);
      bezierVertex(-0.009, 0.07, 0.00, 0.03, 0, 0);
      endShape();
      //foreground leaves
      fill(101, 152, 54);
      ellipse(-0.015, -0.467, 0.15, 0.13);
      ellipse(0.13, -0.37, 0.13, 0.10);
      beginShape();
      vertex(-0.39, -0.32);
      vertex(0.24, -0.70);
      bezierVertex(0.25, -0.69, 0.25, -0.67, 0.24, -0.65);
      vertex(-0.14, -0.38);
      bezierVertex(-0.14, -0.39, -0.13, -0.34, -0.22, -0.36);
      bezierVertex(-0.30, -0.33, -0.33, -0.28, -0.39, -0.32);
      endShape();
      pop();
    }
    scroll() {
      this.x -= this.speed;
      if (this.x < -50) {
        this.x = cwidth + 50;
      }
    }
  }
class Conifer {
    constructor(x, h, speed) {
      this.x = x;
      this.y = cheight - 65;
      this.h = h;
      this.speed = speed || 0.26;
    }
    display() {
      push();
      translate(this.x, this.y);
      scale(this.h);
      noStroke();
      //trunk
      fill(63, 51, 48);
      quad(-0.075, 0, 0.075, 0, 0.06, -0.3, -0.06, -0.3);
      //bottom needles
      fill(60, 148, 52);
      beginShape();
      vertex(0, -1);
      vertex(0.3, -0.15);
      vertex(0.2, -0.17);
      vertex(0.11, -0.12);
      vertex(0, -0.17);
      vertex(-0.11, -0.12);
      vertex(-0.2, -0.17);
      vertex(-0.3, -0.15);
      endShape();
      //middle needles
      fill(69, 170, 60);
      beginShape();
      vertex(0, -1);
      vertex(0.229, -0.35);
      vertex(0.15, -0.37);
      vertex(0.076, -0.33);
      vertex(0, -0.37);
      vertex(-0.076, -0.33);
      vertex(-0.15, -0.37);
      vertex(-0.229, -0.35);
      endShape();
      //top needles
      fill(94, 195, 85);
      beginShape();
      vertex(0, -1);
      vertex(0.164, -0.53);
      vertex(0.109, -0.55);
      vertex(0.055, -0.52);
      vertex(0, -0.55);
      vertex(-0.055, -0.52);
      vertex(-0.109, -0.55);
      vertex(-0.164, -0.53);
      endShape();
      pop();
    }
    scroll() {
      this.x -= this.speed;
      if (this.x < -50) {
        this.x = cwidth + 50;
      }
    }
  }
class Button {
    constructor(x, y, w, h, color, msg, onclick) {
      this.x = x;
      this.y = y;
      this.w = w;
      this.h = h;
      this.color = color;
      this.msg = msg;
      this.onclick = onclick;
    }
    isMouseInside() {
      return mouseX < ((this.x + this.w / 2)*(cwidth/400)) &&
        mouseX > ((this.x - this.w / 2)*(cwidth/400)) &&
        mouseY < ((this.y + this.h / 2)*(cheight/400)) &&
        mouseY > ((this.y - this.h / 2)*(cheight/400));
    }
    display() {
      push();
      translate(this.x, this.y);
      push();
      scale(this.w / 2, this.h / 2);
      if (this.isMouseInside()) {
        stroke(255);
        strokeWeight(0.01);
      } else {
        noStroke();
      }
      fill(this.color);
      rect(0, 0, 2, 2, 0.2);
      pop();
      fill(255);
      text(this.msg, 0, 0);
      pop();
    }
    handleClick() {
      if (this.isMouseInside()) {
        this.onclick();
      }
    }
  }
class Platform {
    constructor(x, y, w) {
      this.x = x;
      this.y = y;
      this.w = w;
    }
    display() {
      noStroke();
      fill(82, 41, 9, 150);
      rect(this.x, this.y, this.w, 15, 7.5);
    }
    scroll() {
      this.x -= 3;
    }
  }
class WaterDrop {
    constructor(x, y) {
      this.x = x;
      this.y = y;
      this.speed = random(2.75, 3);
    }
    display() {
      push();
      translate(this.x, this.y);
      fill(0, 179, 255);
      stroke(255, 255, 255, 150);
      strokeWeight(2);
      ellipse(0, 0, 20, 20);
      beginShape();
      vertex(-10, 0);
      bezierVertex(-8, -11, 0, -10, 0, -18);
      bezierVertex(0, -10, 8, -11, 10, 0);
      endShape();
      curve(0, 5, -7, -2, -3.5, -7, -1, 2);
      pop();
    }
    scroll() {
      this.x -= this.speed;
    }
  }
class Enemy {
    constructor(x, y) {
      this.x = x;
      this.y = y;
    }
    display() {
      push();
      translate(this.x, this.y);
      fill(0, 0, 0, 200);
      ellipse(0, 0, 146, 60);
      fill(135, 16, 16);
      ellipse(0, 10, 70, 20);
      fill(255);
      rect(-10, -17.5, 20, 30, 5);
      rect(10, -17.5, 20, 30, 5);
      rect(-30, -17.5, 20, 25, 5);
      rect(30, -17.5, 20, 25, 5);
      rect(-10, 17.5, 20, 30, 5);
      rect(10, 17.5, 20, 30, 5);
      rect(-30, 17.5, 20, 25, 5);
      rect(30, 17.5, 20, 25, 5);
      fill(247, 111, 145);
      beginShape();
      vertex(-75, -10);
      bezierVertex(-30, -54, 0, -45, 0, -35);
      bezierVertex(0, -45, 30, -54, 75, -10);
      bezierVertex(-13, -22, -13, -22, -75, -10);
      endShape();
      beginShape();
      vertex(-75, 10);
      bezierVertex(-30, 54, 30, 54, 75, 10);
      bezierVertex(-13, 20, -13, 20, -75, 10);
      endShape();
      pop();
    }
    disappear() {
      //this.x = removed from screen
      //wait time? random(5,10) seconds?
      //this.x = back and ready to attack!
    }
  }

let bigCel;
let drop1;
let drop2;
let drop3;
let startBtn;
let mountains;
let trees;
let cel;
let platforms;
let drops;
let scoreDrop;
let enemyTest;

function setup() {
  createCanvas(cwidth, cheight);
  rectMode(CENTER);
  textAlign(CENTER, CENTER);

  enemyTest = new Enemy(200, 200);
  //make celeries
  cel = new Celery(100, 200, 0.3);
  bigCel = new Celery(122, 210, 1.2);
  //make mountains
  var mountain1 = new Mountain(300, 200, 200);
  var mountain2 = new Mountain(400, 200, 150);
  var mountain3 = new Mountain(625, 180, 220, 0.12);
  var mountain4 = new Mountain(550, 120, 100, 0.14);
  var mountain5 = new Mountain(650, 75, 75, 0.11);
  mountains = [mountain1, mountain2, mountain3, mountain4, mountain5];
  //make trees
  var dec1 = new Deciduous(200, 105);
  var dec2 = new Deciduous(249, 110, 0.27);
  var dec3 = new Deciduous(122, 76);
  var dec4 = new Deciduous(325, 43, 0.26);
  var dec5 = new Deciduous(383, 64, 0.24);
  var dec6 = new Deciduous(79, 76);
  var dec7 = new Deciduous(50, 80, 0.26);
  var dec8 = new Deciduous(0, 43, 0.24);
  var dec9 = new Deciduous(470, 100);
  var con1 = new Conifer(344, 100);
  var con2 = new Conifer(300, 50, 0.28);
  var con3 = new Conifer(173, 65);
  var con4 = new Conifer(209, 50);
  var con5 = new Conifer(102, 100, 0.24);
  var con6 = new Conifer(406, 55, 0.23);
  var con7 = new Conifer(440, 76, 0.25);
  trees = [dec1, dec2, dec3, con1, dec4, con2, dec5, con5, dec6, con3, con4, dec7, dec8, con6, con7, dec9];
  //make buttons
  startBtn = new Button(325, 345, 100, 50, color(0, 136, 255), "PLAY", function() {
    currentScene++;
  });
  // make platforms
  platforms = [];
  var platformY = 300 + (cheight - 400);
  for (var i = 0; i < 50; i++) {
    var platformX = i * 200 + 400;
    platforms.push(new Platform(platformX, platformY, random(50, 125)));
    if (platformY > (250 + (cheight - 400))) {
      platformY -= random(25, 50);
    } else if (platformY < (100 + (cheight - 400))) {
      platformY += random(25, 50);
    } else {
      platformY += random(-50, 50);
    }
  }
  for (var j = 0; j < 25; j++) { //backup platforms at bottom of screen
    platforms.push(new Platform(j * 400 + 800, (random(275, 300) + (cheight - 400)), random(100, 200)));
  }
  //make water drops
  drops = [];
  let dropX;
  let dropY;
  for (var k = 0; k < 85; k++) {
    dropX = k * 100 + 200 + random(100, 200);
    if (k < 10) {
      dropY = random(275, 175) + (cheight - 400);
    } else {
      dropY = random(225, 75) + (cheight - 400);
    }
    drops.push(new WaterDrop(dropX, dropY));
  }
  //droplets for start screen
  drop1 = new WaterDrop(0, 0);
  drop2 = new WaterDrop(9, 31);
  drop3 = new WaterDrop(-16, 21);
  //score droplet
  scoreDrop = new WaterDrop(cwidth - 20, 22);
}

function draw() {
  if (currentScene === 0) {
    background(139, 229, 247);
    var green = 160;
    var blue = 255;
    strokeWeight(1);
    for (var i = 0; i < height; i++) {
      stroke(50, green, blue, 150);
      line(0, i, width, i);
      blue -= (255 / height);
      green -= (160 / height);
    }
    push();
    scale(width / 400, height / 400);
    bigCel.display(); //celery
    //water drops
    push();
    translate(230, 100);
    rotate(-PI / 9);
    scale(3);
    drop1.display();
    drop2.display();
    drop3.display();
    pop();
    //text
    push();
    translate(350, 160);
    rotate(PI / 2);
    fill(209, 96, 44);
    textSize(70);
    text("CELERY", 0, 0);
    fill(136, 235, 148);
    textSize(30);
    text("(the game)", -0, 50);
    pop();
    //button display and handling
    startBtn.display();
    mouseClicked = function() {
      startBtn.handleClick();
    }
    //enemyTest.display();
    pop();
  } else if (currentScene === 1) {
        smooth();
        background(139, 229, 247); //sky
        fill(105, 240, 132);
        rect(width/2,height-50,width,100); //grass
        sunWidth = (sin(sunBounce)) * 10 + 60;
        drawSun(65,65+(height-400),sunWidth,sunRot); //sun
        //display mountains
        for (var m = 0; m < mountains.length; m++) {
            mountains[m].scroll();
            mountains[m].display();
        }
        //display trees
        for (var n = 0; n < trees.length; n++) {
            trees[n].scroll();
            trees[n].display();
        }
        keyPressed = function() {
            pressedKeys[keyCode] = true;
        };
        keyReleased = function() {
            pressedKeys[keyCode] = false;
        };
        //moving and jumping
        if (keyIsPressed) {
            if (pressedKeys[37]) {
                cel.jump("l");
            }
            if (pressedKeys[39]) {
                cel.jump("r");
            }
            if (pressedKeys[38] && canJump) {
                cel.jump("u");
                upCount++;
            }
        }
        cel.fall();
        //platform handling
        if (platforms[74].x > 50) {
            for (var k = 0; k < platforms.length; k++) {
                platforms[k].scroll();
                if (platforms[k].x > -100 && platforms[k].x < width + 100) {
                    platforms[k].display();
                    cel.handleCollision(platforms[k]);
                }
            }
        } else { //once there is only one platform left, stop scrolling
            platforms[74].display();
            cel.handleCollision(platforms[74]);
            //display message
            textSize(30);
            fill(171, 48, 199);
            if (score === 85) {
                text("AWESOME JOB!",200,100);
            } else if (score > 76) {
                text("PRETTY GOOD.",200,100);
            } else {
                text("YOU DID OK...",200,100);
            }
            var percent = round(100*(score/85));
            text(percent + "%",200,75);
        }
        
        //water drop displays and checks
        for (var q = 0; q < drops.length; q++) {
            drops[q].scroll();
            if (drops[q].x > -20 && drops[q].x < width + 20) {
                drops[q].display();
                cel.grabDrop(drops[q]);
            }
        }
        cel.display();
        
        //sun rotation and scaling
        sunBounce += PI/90;
        if (sunBounce >= 2*PI) {
            sunBounce = 0;
        }
        sunRot += PI/360;
        if (sunRot >= 2*PI) {
            sunRot = 0;
        }
        
        //stop player from moving up if up button held for more than five frames
        if (upCount > 5) {
            canJump = false;
        }
        //allow player to jump again once on ground
        if (cel.y >= height - 91) {
            canJump = true;
            upCount = 0;
        }
        
        //score display
        fill(0,179,255);
        textSize(25);
        text("SCORE: " + score + "/85", width - 120, 20);
        scoreDrop.display();
    }
}
