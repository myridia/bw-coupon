function set_button()
{
  tinymce.PluginManager.add('mce_button', function( editor, url ) {
  if (editor['id'] === 'bwc_email_message')
  {
    editor.addButton('mce_button_order_id', {
        text: '[Order ID]',
        icon: false,
        onclick: function() {
          editor.insertContent('[order_id]');
       }
    });

    editor.addButton('mce_button_order_date', {
        text: '[Order Date]',
        icon: false,
        onclick: function() {
          editor.insertContent('[order_date]');
       }
    });

    editor.addButton('mce_button_customer', {
        text: '[Customer]',
        icon: false,
        onclick: function() {
          editor.insertContent('[customer]');
       }
    });

  }

  if(editor['id'] === 'giftcoupon_html')
  {

    editor.addButton('mce_button_html', {
        text: '<Generate PDF>',
        icon: false,
        onclick: function(e) {
          const html = document.querySelector('#giftcoupon_html').value;
          generate_pdf(html);
       }
    });


    editor.addButton('mce_button_value', {
        text: '[Add Value]',
        icon: false,
        onclick: function() {
          editor.insertContent('[bwc_value]');
       }
    });

    editor.addButton('mce_button_code', {
        text: '[Add Code]',
        icon: false,
        onclick: function() {
          editor.insertContent('[bwc_code]');
       }
    });

    editor.addButton('mce_button_expire_date', {
        text: '[Add Expire Date]',
        icon: false,
        onclick: function() {
          editor.insertContent('[bwc_expire_date]');
       }
    });


    editor.addButton('mce_button_issue_date', {
        text: '[Add Issue Date]',
        icon: false,
        onclick: function() {
          editor.insertContent('[bwc_issue_date]');
       }
    });



  }
  });
}






set_button();



