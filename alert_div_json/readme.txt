序列化json串
http://stackoverflow.com/questions/2614862/how-can-i-beautify-json-programmatically
demo：http://jsfiddle.net/AndyE/HZPVL/

var obj = {"hello":"world", "Test":["hello"]}

document.body.innerHTML = "";
document.body.appendChild(document.createTextNode(JSON.stringify(obj, null, 4)));

其中JSON.stringify(obj, null, 4)的obj必须是对象而非字符串


鼠标移动到某div显示提示框（包括根据浏览器的顶端和底端来调整显示框的位置）
html中<pre></pre>可以定义预格式化的文本