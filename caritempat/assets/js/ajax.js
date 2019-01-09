var xhr = new XMLHttpRequest();
var x = document.getElementById('bagian-output')

function ambilLokasi() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    }
    else {
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function showPosition(position) {
    if (xhr.readyState == 0 || xhr.readyState == 4) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
        var urlData = 'http://localhost/caritempat/cari.php?lokasi='+ lat + ',' + lng +'&output=json&tipe=coordinates';

        xhr.open('get', urlData, true);
        xhr.onreadystatechange = respon;
        xhr.send();
    }
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            x.innerHTML = "User denied the request for Geolocation."
            break;
        case error.POSITION_UNAVAILABLE:
            x.innerHTML = "Location information is unavailable."
            break;
        case error.TIMEOUT:
            x.innerHTML = "The request to get user location timed out."
            break;
        case error.UNKNOWN_ERROR:
            x.innerHTML = "An unknown error occurred."
            break;
    }
}

function get(url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.onreadystatechange = () => {
        if (xhr.readyState == 4) {
            // defensive check
            if (typeof callback === "function") {
                // apply() sets the meaning of "this" in the callback
                callback.apply(xhr);
            }
        }
    };
    xhr.send();
}

function getPhoto(val) {
    get('http://localhost/caritempat/getphoto.php?id=' + val + '&output=json',
        function () {
            var resp = JSON.parse(this.responseText);

            var modal = document.querySelector('#modal-image');

            var html = document.querySelector('html');

            modal.classList.add('is-active');

            html.classList.add('is-clipped');

            if (resp['urlPhoto'] != 'original') {
                var data = '<img src="' + resp['urlPhoto'] + '" alt="image">';
            }
            else {
                var data = '<img src="assets/img/notfound.jpg" alt="image">';
            }

            document.getElementById('modal-bg').addEventListener('click', () => {
                html.classList.remove('is-clipped');
            })

            document.getElementById('modal-close').addEventListener('click', () => {
                html.classList.remove('is-clipped');
            })

            document.getElementById('image-venue').innerHTML = data;
        }
    )
}

function proses(e) {
    if (xhr.readyState == 0 || xhr.readyState == 4) {
        if (event.keyCode === 13) {
            var lokasi = e.value;
            var urlData = 'http://localhost/caritempat/cari.php?lokasi='+lokasi+'&output=json&tipe=near';

            xhr.open('get', urlData, true);
            xhr.onreadystatechange = respon;
            xhr.send();
        }
    }
}

function respon() {
    if (xhr.readyState == 4) {
        if (xhr.status == 200) {
            var doc = JSON.parse(xhr.responseText);

            var data = '';

            for (var i = 0; i < doc['rows']; i++) {
                data += '<div class="column is-one-third">';
                data += '<div class="card large is-shady">';
                data += '<div class="card-image">';
                data += '<div class="card-content">';
                data += '<div class="content">';
                data += '<h3>' + doc['data'][i]['name'] + '</h3>';
                data += '<p><b>Latitude</b> : ' + doc['data'][i]['location']['lat'] + '</p>';
                data += '<p><b>Longitude</b>: ' + doc['data'][i]['location']['lng'] + '</p>';
                if (doc['data'][i]['location']['postalCode'] != undefined) {
                    data += '<p><b>Kode Pos</b>: ' + doc['data'][i]['location']['postalCode'] + '</p>';
                }
                else {
                    data += '<p><b>Kode Pos</b>: Tidak ditemukan</p>';
                }
                if (doc['data'][i]['categories'].length > 0) {
                    data += '<p><b>Kategori</b>: ' + doc['data'][i]['categories'][0]['name'] + '</p>';
                }
                else {
                    data += '<p><b>Kategori</b>: Belum dikategorikan</p>';
                }
                data += '<button class="button is-small is-link modal-button" onclick="getPhoto(\'' + doc['data'][i]['id'] + '\')">Lihat Foto</button>';
                data += '</div>';
                data += '</div>';
                data += '</div>';
                data += '</div>';
                data += '</div>';
            }

            document.getElementById('bagian-output').innerHTML = data;
        }
        else{
            alert('Sorry, error: ' + xhr.status);
        }
    }
    else {
        document.getElementById('bagian-output').innerHTML = 'tunggu...';
    }
}