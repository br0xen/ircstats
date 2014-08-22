function ajaxJSON(url, params) {
  var promise, d = new Date(), n;
  n = d.getTime();
  var params = (params==undefined)?{'type':'GET'}:params;
  params.type = (params.type==undefined)?'GET':params.type.toUpperCase();
  params.data = (params.data==undefined)?{}:params.data;
  params.encode_data = (params.encode_data==undefined)?true:params.encode_data;
  var p = params.encode_data?paramsToString(params.data):params.data;
  promise = new Promise(function(resolve, reject) {
    var client = new XMLHttpRequest();
    client.open(params.type, url, true);
    client.onreadystatechange = function() {
      if(this.readyState === this.DONE) {
        var resp = (this.response!==undefined)?this.response:this.responseText;
        try {
          var json_resp = JSON.parse(resp);
        } catch(e) {
          json_resp = {"error":"A JSON Parsing Error Occurred."};
        }
        resolve(json_resp);
      }
    };
    client.send(p);
  });
  return promise;
}

function paramsToString(params, parent_key) {
  parent_key = (parent_key==undefined)?"":parent_key;
  var p = "";
  for(var i in params) {
    var key = i;
    if(p.length > 0) { p+="&"; }
    if(typeof params[i] === 'object') {
      if(parent_key.length > 0) { key = parent_key+'['+i+']'; }
      p+=paramsToString(params[i], key);
    } else {
      if(parent_key.length > 0) { key = parent_key+'['+i+']'; }
      p+=key+'='+params[i];
    }
  }
  return p;
}
