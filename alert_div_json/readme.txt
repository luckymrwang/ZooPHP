���л�json��
http://stackoverflow.com/questions/2614862/how-can-i-beautify-json-programmatically
demo��http://jsfiddle.net/AndyE/HZPVL/

var obj = {"hello":"world", "Test":["hello"]}

document.body.innerHTML = "";
document.body.appendChild(document.createTextNode(JSON.stringify(obj, null, 4)));

����JSON.stringify(obj, null, 4)��obj�����Ƕ�������ַ���


����ƶ���ĳdiv��ʾ��ʾ�򣨰�������������Ķ��˺͵׶���������ʾ���λ�ã�
html��<pre></pre>���Զ���Ԥ��ʽ�����ı�