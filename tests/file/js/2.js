(function() {
  setTimeout(
  function() {
    let preread = document.querySelectorAll("a.optimized");
    preread.forEach(function(k, v){
            var rel = document.createElement ("link");
            if (navigator.which.vendor === 'Chrome' && navigator.which.version > 54) {
                rel.setAttribute("rel", "prerender");
            } else {
                rel.setAttribute("rel", "prefetch");
            }
            rel.setAttribute("href", k.href);
            document.getElementsByTagName("head")[0].appendChild(rel);
        });
  }, 200);
}());
