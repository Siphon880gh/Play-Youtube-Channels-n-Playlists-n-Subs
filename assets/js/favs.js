class More { // static
    constructor() {
      if(typeof window.count==='undefined') window.count=-1;
    }
    count() {
      window.count++; 
      if(window.count>=2) this.reached();
      setTimeout(()=>{
        window.count=-1;
      }, 3000);
    }
    reached() {
      window.count=-1;
  
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        // document.getElementById("demo").innerHTML = this.responseText;
          var responseText = this.responseText;
          document.querySelector("#modal-more .contents").innerHTML = responseText;
        }
      };
      xhttp.open("GET", prependPath+"list-favs.php?prependPath=" + prependPath, true);
      xhttp.send();
  
  
      document.querySelector("#modal-more").classList.remove("fade");
    }
  }

  class ManualPlaylist {
    constructoor() { }
    prompt() {
      var p = prompt("Enter a playlist ID:");
      if(p && p.length)
        window.parent.urlChange.playlist(p);
    } // prompt
  }
  ManualPlaylist = new ManualPlaylist();