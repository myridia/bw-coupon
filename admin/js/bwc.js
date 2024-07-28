jQuery(document).ready(function($){

  jQuery( ".bwc_preview_pdf" ).click( async function(e) {
    const id = this.parentNode.dataset.coupon;
    const domain = this.parentNode.dataset.domain;
    await get_pdf(id,domain);
  });


  jQuery( ".bwc_pdf" ).click( async function(e) {
    const id = this.dataset.coupon;
    const domain = this.dataset.domain;
    await get_pdf(id,domain);
  });

  jQuery( ".bwc_email_pdf" ).click( async function(e) {
    const id = this.parentNode.dataset.coupon;
    const domain = this.parentNode.dataset.domain;
    await send_pdf(id,domain);
  });

  jQuery( ".bwc_send_email_btn" ).click( async function(e) {
    const id = this.dataset.coupon;
    const domain = this.dataset.domain;
    const mailto =  jQuery("#bwc_coupon_"+id).val();
    await send_pdf(id,domain,mailto);
  });



  if( jQuery("#giftcoupon").prop('checked') ) //Product as coupon show or hide the editor 
  {
    jQuery('.bwc_coupon_options').show();
  }
  else
  {
    jQuery('.bwc_coupon_options').hide();
  }


  jQuery('#giftcoupon').change(function(){
    if(jQuery("#giftcoupon").prop('checked') == true)
    {
     jQuery('.bwc_coupon_options').show();
    }
    else
    {
     jQuery('.bwc_coupon_options').hide();
    }
  });




});

async function send_pdf(_id,domain,mailto=false)
{
  //let url = domain + '/wp-json/bwc/v1/send_pdf/' + _id;
  let url = domain + '/?rest_route=/bwc/v1/send_pdf/' + _id;
  if(mailto)
  {
    //url = domain + '/wp-json/bwc/v1/send_pdf/'+_id+'?email=' + mailto;
    url = domain + '/?rest_route=/bwc/v1/send_pdf/'+_id+'?email=' + mailto;
  }

  let r = JSON.parse(await aget_api(url));
  if(r['send'])
  {
    alert(r['msg']);
  }
  else
  {
    alert(r['msg']);
  }

}

async function get_pdf(_id,domain)
{
  let filename = _id  + '.pdf';
  //let url = domain + '/wp-json/bwc/v1/pdf/'+_id;
  let url = domain + '/?rest_route=/bwc/v1/pdf/'+_id;
  let r = JSON.parse(await aget_api(url));
  download_pdf(r['pdf'],filename);
}


async function encode_uri(data)
{
  let s = '';
  for(let i in data)
  {
    s += i + '=' + encodeURIComponent(data[i]) + '&';
  }
  return s.substring(0, s.length-1);
}

/* preview of the pdf in admin */
async function generate_pdf(html)
{
  //html = "<body>foo</bod>";
  const domain = document.querySelector('#coupon_design').dataset.domain;
  let e = document.getElementById('title');
  let filename = e.value.replace(/[^a-zA-Z0-9-_]/g, "_").replace('__','_') + '.pdf';
  //let url = domain + '/wp-json/bwc/v1/pdf';
  let url = domain + '/?rest_route=/bwc/v1/pdf';
  let data = await encode_uri({
      id:html,
      title:'pdf_preview'
  });
  let r = JSON.parse(await apost_api(url,data));
  download_pdf(r['pdf'],filename);
}

function download_pdf(pdf,filename) {
  const source = `data:application/pdf;base64,${pdf}`;
  const link = document.createElement("a");
  link.href = source;
  link.download = filename;
  link.click();
}


async function aget_api(url)
{
  return new Promise((resolve, reject) => {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", url,true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
     return resolve(xhr.responseText);
    };
    xhr.onerror = function() {
      return reject(xhr.statusText);
    };
    xhr.send(null);
  });
}


async function apost_api(url,data)
{
  return new Promise((resolve, reject) => {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", url);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = () => resolve(xhr.responseText);
    xhr.onerror = () => reject(xhr.statusText);
    xhr.send(data);
  });
}
