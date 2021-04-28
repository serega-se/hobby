$(function () {

    let list = [];
    let postCount = 0;
    let currentPage = 0;
    let numberPerPage = 5;

    function getList(page) {
        let url = "/api/v1/posts";
        if (page > 0) {
            url = url.concat("?page=", page.toString());
        }
        requestPost(url);
    }

    function uploadNew() {
        let url = "/api/v1/posts/upload";
        requestPost(url);
        return null;
    }

    function requestPost(url) {
        $.getJSON(url, function (data) {
            list = data.items;
            postCount = data.count;
            currentPage = data.current;
            numberPerPage = data.onpage;

            drawList();
            drawPaginate();
        });
    }


    function showPostText(url) {
        $.get(url, function (data) {
            $('.modal-body').html(response);
            $('#exampleModalLong').modal('show');
        });
    }

    function getNumberOfPages() {
        return Math.ceil(postCount / numberPerPage);
    }

    function drawList() {
        let root = document.getElementById("post-list");
        let tmpl = document.getElementById('tmpl');
        root.innerHTML = "";

        list.forEach(function (item, i, list) {
            let clone = document.importNode(tmpl.content, true);
            clone.querySelector("h5.post-header").innerHTML = item.header;
            clone.querySelector("p.post-content").innerHTML = item.text.substr(200);
            clone.querySelector("a.post-link").setAttribute("href", item.link);
            clone.querySelector("a.post-modal-show").onclick = function () {
                $.get("/api/v1/posts/content?id=".concat((item.id).toString()), function (data) {
                    $('.modal-title').html(item.header);
                    $('.modal-body').html(data.content);
                    $('#exampleModalLong').modal('show');
                });
                return false;
            };
            root.appendChild(clone);
        });
    }

    function drawPaginate() {
        let root = document.getElementById("page-paginate");
        let tmpl = document.getElementById('paginate-tmpl');
        root.innerHTML = "";

        for (let i = 0; i < getNumberOfPages(); i++) {
            let clone = document.importNode(tmpl.content, true);
            clone.querySelector(".page-link").textContent = (i + 1).toString();
            clone.querySelector(".page-link").dataset.pagenum = i.toString();
            clone.querySelector(".page-link").onclick = function () {
                let page = this.dataset.pagenum > 0 ? this.dataset.pagenum : 0;
                getList(page);
                return false;
            };
            root.appendChild(clone);
        }
    }

    function load() {
        getList(0);
    }

    window.onload = load;

    document.querySelector("#upload-btn").onclick = function () {
        uploadNew();
        return false;
    }

});
