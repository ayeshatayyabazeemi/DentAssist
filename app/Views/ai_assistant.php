<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dental AI Assistant</title>

<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<link rel="icon" type="image/png" href="<?= base_url('assets/images/mylogo.png') ?>">

<style>

body{
font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif;
background:#ffe6f0;
margin:0;
padding:0;
color:#333;
}

.container{
max-width:800px;
margin:50px auto;
padding:30px 40px;
background:#fff0f6;
border-radius:15px;
box-shadow:0 8px 25px rgba(0,0,0,0.1);
}

h2{
text-align:center;
margin-bottom:30px;
color:#d6336c;
}

label{
font-weight:600;
margin-bottom:8px;
display:block;
color:#a8325e;
}

textarea{
width:100%;
min-height:130px;
padding:15px;
border-radius:10px;
border:1px solid #f8c0d9;
font-size:16px;
resize:vertical;
}

textarea:focus{
border-color:#d6336c;
outline:none;
box-shadow:0 0 8px rgba(214,51,108,0.3);
}

button{
background:#ffb6c1;
color:#d6336c;
border:none;
padding:14px 25px;
border-radius:8px;
cursor:pointer;
font-size:16px;
margin-top:20px;
width:100%;
font-weight:600;
}

button:hover{
background:#ff9eb6;
}

button:disabled{
background:#ffd6e0;
cursor:not-allowed;
}

.result-box{
margin-top:30px;
padding:25px;
background:#fff0f6;
border-left:5px solid #d6336c;
border-radius:10px;
display:none;
}

.loading{
font-style:italic;
}

strong{
color:#d6336c;
}

.suggestion-wrapper{
position:relative;
}

.suggestion-box{
position:absolute;
top:100%;
left:0;
right:0;
background:white;
border:1px solid #f8c0d9;
border-radius:6px;
max-height:200px;
overflow-y:auto;
display:none;
z-index:1000;
}

.suggestion-item{
padding:8px 10px;
cursor:pointer;
border-bottom:1px solid #f3d3e2;
}

.suggestion-item:hover{
background:#ffe6f0;
}

</style>
</head>

<body>

<div class="container">

<h2>Dental Assistant</h2>

<label>Enter Patient Symptoms:</label>

<div class="suggestion-wrapper">
<textarea id="symptoms" placeholder="e.g., tooth pain, bleeding gums, swollen jaw..."></textarea>
<div id="suggestionBox" class="suggestion-box"></div>
</div>

<button id="analyzeBtn">Get Suggestions</button>

<div class="result-box" id="resultBox"></div>

</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<script>

const notyf = new Notyf()

/* KEYWORDS */

const keywords = [
'cavity','decay','hole in tooth','tooth pain','black spot','tooth damage',
'sugar cavity','tooth rot','bleeding gums','swollen gums','gum pain',
'red gums','gum inflammation','tender gums','gum bleeding',
'loose teeth','gum recession','sensitive teeth','pain hot','pain cold',
'pain sweet','ice cream tooth pain','hot drink pain','sharp tooth pain',
'bad breath','stinky mouth','halitosis','foul smell','pus','tooth infection',
'pain on biting','wisdom teeth pain','jaw pain','impacted tooth',
'cracked tooth','broken tooth','chipped tooth','fracture','crooked teeth',
'overbite','underbite','misaligned bite','crossbite','crowded teeth',
'grinding teeth','night grinding','clenching teeth','mouth sores',
'ulcers','canker sores','white patches','open sore','mouth lesion',
'enamel loss','acid wear','tooth thinning','yellow teeth','stains',
'coffee stains','tea stains','smoking stains','dry mouth','sticky mouth',
'saliva deficiency','cotton mouth','jaw clicking','jaw locking',
'tongue lesion','missing tooth','gap in teeth','root exposure',
'plaque','tartar','calculus','sticky teeth','dry socket',
'post extraction pain','bad taste after extraction','mouth infection',
'teething pain','child tooth pain','uneven smile','jaw injury',
'facial ache','weak enamel','extra teeth','supernumerary teeth',
'tooth agenesis','fluorosis','impacted canine','tooth tumor',
'jaw mass','cleft palate','tooth fracture','pulp inflammation',
'tongue tie','restricted tongue','speech difficulty','pericoronitis',
'tongue coating','jaw cyst','eruption cyst','gum overgrowth',
'periapical abscess','fused tooth','lichen planus'
]

const textarea = document.getElementById("symptoms")
const suggestionBox = document.getElementById("suggestionBox")

textarea.addEventListener("input", function(){

const text = textarea.value.toLowerCase()

const words = text.split(" ")
const currentWord = words[words.length - 1]

suggestionBox.innerHTML=""

if(currentWord.length < 2){
suggestionBox.style.display="none"
return
}

const matches = keywords.filter(k => k.includes(currentWord)).slice(0,8)

if(matches.length === 0){
suggestionBox.style.display="none"
return
}

matches.forEach(word=>{

const div=document.createElement("div")
div.classList.add("suggestion-item")
div.innerText=word

div.onclick=()=>{

words[words.length-1]=word
textarea.value=words.join(" ")+" "

suggestionBox.style.display="none"

}

suggestionBox.appendChild(div)

})

suggestionBox.style.display="block"

})

document.addEventListener("click",function(e){

if(!textarea.contains(e.target) && !suggestionBox.contains(e.target)){
suggestionBox.style.display="none"
}

})

/* API CALL */

const analyzeBtn=document.getElementById("analyzeBtn")
const resultBox=document.getElementById("resultBox")

analyzeBtn.addEventListener("click",async()=>{

const symptoms=textarea.value.trim()

if(!symptoms){
notyf.error("Please enter symptoms")
return
}

analyzeBtn.disabled=true

resultBox.style.display="block"
resultBox.innerHTML='<span class="loading">Analyzing symptoms...</span>'

try{

const res=await fetch('/api/ai-assistant',{
method:'POST',
headers:{'Content-Type':'application/json'},
body:JSON.stringify({symptoms})
})

const json=await res.json()

if(json.status==='success'){

const suggestions=json.suggestion

if(typeof suggestions==='string'){
resultBox.innerHTML=suggestions
}else{

let html=''

suggestions.forEach((s,i)=>{

html+=`
<strong>Problem ${i+1}:</strong> ${s.problem}<br>
<strong>Treatment:</strong> ${s.treatment}<br>
<strong>Recommended Lab Tests:</strong> ${s.lab_tests}<br>
<strong>Reason:</strong> ${s.reason}<br><hr>
`

})

resultBox.innerHTML=html

}

}else{
resultBox.innerHTML='Error getting suggestion'
}

}catch(e){

resultBox.innerHTML='Server error'

}

analyzeBtn.disabled=false

})

</script>

</body>
</html>
