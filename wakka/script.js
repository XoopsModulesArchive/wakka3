function fKeyDown()
{
if (event.keyCode == 9)
{
event.returnValue = false;
document.selection.createRange().text = String.fromCharCode(9);
}
}
function move(add,del){
var key=new Array;
var val=new Array;
if (del.options.selectedIndex==-1)
del.options.selectedIndex=del.options.length-1;
if(del.options.length && del.options[del.options.selectedIndex].value!=''){
add.options.length=add.options.length+1;
add.options[add.options.length-1].value=del.options[del.options.selectedIndex].value;
add.options[add.options.length-1].text=del.options[del.options.selectedIndex].text;
counter=0;
for (var i=0;i<del.options.length;i++){
if (!del.options[i].selected){
key[counter] = del.options[i].text;
val[counter] = del.options[i].value;
counter++;
}
}
del.options.length = del.options.length -1;
for (i in key){
del.options[i].text = key[i];
del.options[i].value = val[i];
}
}
}
function commit(admins,admin){
var value='';
var flag=true;
admin.value='';
for (var i=0;i<admins.options.length;i++){
if (admins.options[i].value=="*")
flag=false;
}
if (flag){
for (var i=0;i<admins.options.length;i++){
if (i==admins.options.length-1)
admin.value=admin.value+admins.options[i].value;
else
admin.value=admin.value+admins.options[i].value+'|';
}
}
}