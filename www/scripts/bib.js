function zoom(xmi, xma, ymi, yma)
{
  xMin = xmi;
  xMax = xma;

  yMin = 0;
  yMax = 0;

  var screenRatio   = totalWidth/totalHeight;
  var displayWidth  = xMax-xMin;
  var displayHeight = displayWidth/screenRatio;

  if (arguments.length == 2)//  (typeof(ymi) == 'undefined') && (typeof(yma) == 'undefined'))
  {
    yMin = -(displayHeight/2);
    yMax = +(displayHeight/2);
  }
  else
  {
    yMin = ymi;
    if (arguments.length == 4) //(typeof(yma) != 'undefined')
      yMax = yma;
    else
      yMax = yMin+displayHeight;
  }
}

function axes()
{
  newline(0, yMin, 0, yMax);
  newline(xMin, 0, xMax, 0);

  var length = step/10;
  
  var x = 0;
  while (x <= xMax)
  {
    newline(x, 0-length, 
            x, 0+length);
    
    x += step;
  }
  
  var x = 0;
  while (x >= xMin)
  {
    newline(x, 0-length, 
            x, 0+length);
    
    x -= step;
  }
  
  var y = 0;
  while (y <= yMax)
  {
    newline(0-length, y, 
            0+length, y);
    
    y += step;
  }
  
  var y = 0;
  while (y >= yMin)
  {
    newline(0-length, y, 
            0+length, y);
    
    y -= step;
  }  
}


function getCoordinate(x, y)
{
  var xSize = xMax - xMin;
  var ySize = yMax - yMin;
  
  var xDiff = x - xMin;
  var yDiff = y - yMin;
  
  var xPos = xDiff / xSize * totalWidth;
  var yPos = totalHeight - yDiff / ySize * totalHeight;

  return {x: xPos, y: yPos};
}

function getSize(size)
{
  var xSize = xMax - xMin;
  var virtualRatio = size/xSize;

  return virtualRatio * totalWidth;
}

function point(x, y, color, size)
{
  var color = typeof(color) != 'undefined' ? color : "black";
  var size = typeof(size) != 'undefined' ? getSize(size) : 1;
  var p = getCoordinate(x, y);
  circle(p.x, p.y, size, color);
}

function newline(x1, y1, x2, y2, thickness, color)
{
  var color = typeof(color) != 'undefined' ? color : "black";
  var thickness = typeof(thickness) != 'undefined' ? getSize(thickness) : 1;
  var p1 = getCoordinate(x1, y1);
  var p2 = getCoordinate(x2, y2);

  line(p1.x, p1.y, p2.x, p2.y, thickness, color);
}

function print(text)
{
  $("#code-output", window.parent.document).append(text);
  $("#lower-right", window.parent.document).scrollTop($("#lower-right", window.parent.document)[0].scrollHeight);
}

function println(text)
{
  print(text+"\n");
}

function clear()
{
  $("#code-output", window.parent.document).html("");
}

function plotTable(t, thickness, color)
{
  if (typeof(thickness) == 'undefined')
  {
    for (index in t)
      if (!isNaN(index))
        point(index, t[index]);
  }
  else
  {
    if (typeof(color) == 'undefined')
      color = "black";

    for (index in t)
      if (!isNaN(index))
        newline(index, yMin, index, t[index], thickness, color);    
  }
}

Array.prototype.sum = function()
{
    var sum = 0;
  
    for (index in this)
        if (!isNaN(index))
            sum += this[index];
  
    return sum;
}

Array.prototype.max = function()
{
    var max = -1/0;
  
    for (index in this)
        if (!isNaN(index))
            if (this[index] > max)
                max = this[index];
  
    return max;
}

step = 10;

nextStep = requestAnimationFrame;