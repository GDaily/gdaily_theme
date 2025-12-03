tinymce.PluginManager.add('gd_url_box', function (editor) {
    editor.addButton('gd_url_box_button', {
        title: '插入相關文章',
        text: '相關文章',
        onclick: function () {
            editor.windowManager.open({
                title: '插入相關文章',
                body: [
                    {
                        type: 'textbox',
                        name: 'postid',
                        label: '文章 ID',
                        value: ''
                    }
                ],
                onsubmit: function (e) {
                    var postid = e.data.postid.trim();
                    if (!postid || isNaN(postid)) {
                        alert('請輸入有效的文章 ID');
                        return false;
                    }
                    editor.insertContent('[url_box postid="' + postid + '"]');
                }
            });
        }
    });
});
