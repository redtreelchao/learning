


export default const Ajax =() => {
	var http;
	http=new XMLHttpRequest();
	http.onreadystatechange = ()=>{
		if (http.readyState==4 && http.status == 200 || http.status ==304) {
			
		}	
	}
} 