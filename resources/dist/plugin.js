function r(){return([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g,l=>(l^crypto.getRandomValues(new Uint8Array(1))[0]&15>>l/4).toString(16))}function d(l,i){return{id:null,state:null,statePath:null,wire:null,disabled:!1,progress:0,processing:!1,img:{src:null,key:null,blob:null},fileKeyIndex:{},init(){this.id=i.id,this.statePath=i.statePath,this.disabled=i.disabled,this.wire=i.$wire,this.state=l,this.getFiles()},async getFiles(){this.processing=!0;let e=await i.getFormUploadedFiles();this.fileKeyIndex=e??{},this.img.key=Object.keys(this.fileKeyIndex)[0],this.img.src=this.img.key?this.fileKeyIndex[this.img.key].url:null,this.processing=!1},async deleteFile(){this.img.blob=null,this.img.key&&(await i.deleteUploadedFileUsing(this.img.key),this.img.src=null,this.img.key=null)},uploadFile(e){this.processing=!0,this.progress=0,this.readFile(e.target.files[0],(async(a,s)=>{let n=r();this.processing=!0,this.img.blob=s,await i.uploadUsing(n,a,t=>{this.processing=!1,this.img.key=t},t=>{console.log("error: "+t)},t=>{console.log(t)})}).bind(this))},uploadSuccess(e,a){this.img.dataUri=!0,this.img.src=e,this.processing=!1},readFile(e,a){var s=new FileReader;s.onload=()=>a(e,s.result),s.readAsDataURL(e)}}}export{d as default};
