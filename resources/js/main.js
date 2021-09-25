// gltf-validator
const validator = require('gltf-validator');


// Boostrap custom file input
import bsCustomFileInput from 'bs-custom-file-input';
bsCustomFileInput.init();





//--------------------------------------------------//
//                  CONFIGS PAGE                    //
//------------------------------- ------------------//





(function() {
  "use strict";

  var configform = document.getElementById('config-form');

  if (configform) {

    // Theme color
    var themecolor = document.getElementById('themecolor'),
      themecolor2 = document.getElementById('themecolor2');

    themecolor.addEventListener('keyup', function (e) {
      themecolor2.value = this.value;
    }, false);

    themecolor2.addEventListener('change', function (e) {
      themecolor.value = this.value;
    }, false);


    // Icon preview
    var themeicon = document.getElementById('themeicon');
    themeicon.addEventListener('change', function (e) {
      var file = e.target.files[0],
        img = document.getElementById('themeicon-preview'),
        reader = new FileReader();
      reader.onload = (function (aImg) {
        return function (e) {
          aImg.src = e.target.result;
        };
      })(img);
      reader.readAsDataURL(file);
    }, false);


    // Logo preview
    var themelogo = document.getElementById('themelogo');
    themelogo.addEventListener('change', function (e) {
      var file = e.target.files[0],
        img = document.getElementById('themelogo-preview'),
        reader = new FileReader();
      reader.onload = (function (aImg) {
        return function (e) {
          aImg.src = e.target.result;
        };
      })(img);
      reader.readAsDataURL(file);
    }, false);

  }

})();





//--------------------------------------------------//
//               CREATE SCENE PAGE                  //
//------------------------------- ------------------//





(function ($) {
  "use strict";

  if ($('#scene-page').length) {

    // ------  Start Page -------- //

    handleExtensions();

    // Get Entities and attach to scene and form
    // Get Marker or create one
    var scene_id = $('#scene_id').val();
    
    axios({
      method: 'get',
      url: `/scenes/${scene_id}`,
      headers: {
        'Accept': 'application/json'
      },
    }).then(function (response) {

      var scene = response.data.scene;

      // ------ ENTITIES ------ //

      // Get Entities and attach to scene and form
      if(scene.markers.length > 0) {
        if (scene.markers[0].entities.length) {
          var entities = scene.markers[0].entities,
            response_data = {};
          for (var i = 0; i < entities.length; i++) {

            response_data.entity = entities[i];

            // Create Entity Object of Scene and Attach to scene
            var entityObj = createEntityObject(response_data);

            // Create Entity Form and Attach events to Entity Obj
            createEntityForm(entityObj, response_data);
          }
        }
      }

      // ------ MARKER ------ //

      // Get Marker or create one
      if (scene.markers.length == 0) {
        // Create one
        axios({
          method: 'post',
          url: '/markers',
          headers: {
            'Accept': 'application/json'
          },
          data: {
            scene_id: $('#scene_id').val()
          }
        }).then(function (response) {
          setMarker(response.data.marker);
        }).catch(function (error) {
          console.log(error);
        });

      } else {
        setMarker(scene.markers[0]);
      }

    }).catch(function (error) {
      if (error.response) {
        showErrors(error);
      }
    });

    // ------ End Start Page -------- //


    // MARKER EVENTS

    $('#btn-change-marker').on('click', function() {

      $('#custom-markers')[0].innerHTML = '';

      axios({
        method: 'get',
        url: '/mycustommarkers?type=json&timestamp=' + ((new Date).getTime()),
        headers: {
          'Accept': 'application/json'
        },
      }).then(function (response) {
  
        var cm = response.data.custom_markers,
            lines = Math.ceil(cm.length/4),
            cols = 4,
            quantityOfMarkers = cm.length,
            index = 0;

        for (var i = 0; i < lines; i++) {
          var row = '<div class="row">';
          for (var j = 0; j < cols; j++) {
            if(index < quantityOfMarkers) {
              row += `
                <div class="col col-md-3">
                  <div class="form-check">
                    <input class="form-check-input custom-marker" 
                            type="radio" 
                            id="${cm[index].id}" 
                            name="marker" 
                            value="${cm[index].id}" 
                            data-default="0" 
                            data-num="${cm[index].id}">
                    <label class="form-check-label" for="${cm[index].id}">
                      <img src="${cm[index].thumb}" class="img-fluid">
                    </label>
                  </div>
                </div>`;
            } else {
              row += '<div class="col col-md-3"></div>';
            }
            index++;
          }
          row += '</div>';
          $('#custom-markers').append(row);
        } 
      });
    });


    $('#btn-select-marker').on('click', function() {
      $('.custom-marker').each(function(index, elem) {
        if($(elem).prop('checked')) {
          axios({
            method: 'post',
            url: '/markers/' + $('#marker_id').val(),
            headers: {
              'Accept': 'application/json'
            },
            data: {
              _method: 'put',
              marker_id: $('#marker_id').val(),
              scene_id: $('#scene_id').val(),
              default: elem.dataset.default,
              num: elem.dataset.num
            }
          }).then(function (response) {
            setMarker(response.data.marker);
          }).catch(function (error) {
            showErrors(error);
          });
        }
      });
    });
    

    $('#btn-create-custom-marker').on('click', function() {
      $('#changeMarkerModal').modal('hide');
    });

    // Marker input radio buttons
    var qpr = $('#qrcode_print_row');
    $('#download_type_png').on('change', function () {
      qpr.prop('hidden', true);
    });

    var dpdf = $('#download_type_pdf');
    dpdf.on('change', function () {
      qpr.prop('hidden', false);
    });


    $('#qrcode_preview').on('click', function (e) {
      var marker_id = $('#marker_id').val(),
        download_type = $('#download_type_pdf').prop('checked') ? 'pdf' : 'png',
        baseURL = document.head.querySelector('meta[name="app-url"]').content,
        href = baseURL + '/markers/' + marker_id + '/download?preview=1&download_type=' + download_type;

      if (download_type == 'pdf') {
        var quantity_markers = $('#quantity_markers').val(),
          marker_size = $('#marker_size').val();
        href += '&quantity_markers=' + quantity_markers + '&marker_size=' + marker_size;
      }

      window.open(href);
      e.preventDefault();
    });


    $('#qrcode_download').on('click', function (e) {
      var marker_id = $('#marker_id').val(),
        download_type = $('#download_type_pdf').prop('checked') ? 'pdf' : 'png',
        baseURL = document.head.querySelector('meta[name="app-url"]').content,
        href = baseURL + '/markers/' + marker_id + '/download?preview=0&download_type=' + download_type;

      if (download_type == 'pdf') {
        var quantity_markers = $('#quantity_markers').val(),
          marker_size = $('#marker_size').val();
        href += '&quantity_markers=' + quantity_markers + '&marker_size=' + marker_size;
      }

      window.open(href);
      e.preventDefault();
    });


    // ------ END MARKER EVENTS ------ //



    // -------- CHANGE CAMERA VIEW ---------//
    // var sceneEl = document.querySelector('a-scene');
    // var btnChangeView = document.querySelector('#btnChangeView');
    // btnChangeView.addEventListener('click', function() {
    //   var pos = sceneEl.camera.getAttribute('position');
    // });


    // -------- END CHANGE CAMERA VIEW ---------//


    // Add Entity on Click (Dropdown button)

    var entTypes = $('.dropdown-item');
    entTypes.each(function (index, elem) {

      $(elem).on('click', function (e) {

        var type = this.dataset.type,
          accept = this.dataset.accept,
          inputFile = $('<input type="file">');

        inputFile[0].accept = accept;
        inputFile[0].click();

        inputFile.on('change', function () {
          addEntity(this.files[0], type);
        });
        e.preventDefault();
      });
    });



    // SAVE SCENE AND ENTITIES
    $('#save-scene').on('click', function (e) {
      saveScene();
      e.preventDefault();
    });



    // ------------ FUNCTIONS ----------- //


    function addEntity(file, type) {
      var marker_id = $('#marker_id').val();
      var formData = new FormData();
      formData.append('name', file.name);
      formData.append('marker_id', marker_id);
      formData.append('props_asset_size', file.size);
      formData.append('props_asset_file', file);

      switch (type) {
        case 'image':
          var img = new Image();
          img.onload = function () {
            formData.append('type', 'a-image');
            formData.append('props_asset_width', img.naturalWidth);
            formData.append('props_asset_height', img.naturalHeight);
            formData.append('props_asset_type', 'img');
            var fileData = {
              width: img.naturalWidth,
              height: img.naturalHeight,
              size: file.size
            };
            var res = validateFile(fileData);
            if (res === true) {
              submitEntity(formData);
            } else {
              showErrors(res);
            }
          }
          var reader = new FileReader();
          reader.onload = (function (aImg) {
            return function (e) {
              aImg.src = e.target.result;
            };
          })(img);
          reader.readAsDataURL(file);
          break;

        case 'video':
          var video = document.createElement('video');
          video.onloadedmetadata = function () {
            formData.append('type', 'a-video');
            formData.append('props_asset_type', 'video');
            formData.append('props_asset_width', this.videoWidth);
            formData.append('props_asset_height', this.videoHeight);
            formData.append('props_asset_duration', Math.floor(this.duration));
            formData.append('props_asset_loop', 'false');
            formData.append('props_asset_preload', 'none');
            formData.append('props_asset_autoplay', 'false');
            var fileData = {
              width: this.videoWidth,
              height: this.videoHeight,
              duration: Math.floor(this.duration),
              size: file.size
            };
            var res = validateFile(fileData);
            if (res === true) {
              submitEntity(formData);
            } else {
              showErrors(res);
            }
          }
          var reader = new FileReader();
          reader.onload = (function (aVid) {
            return function (e) {
              aVid.src = e.target.result;
            };
          })(video);
          reader.readAsDataURL(file);
          break;

        case 'audio':
          var audio = document.createElement('audio');
          audio.onloadedmetadata = function () {
            formData.append('type', 'a-sound');
            formData.append('props_asset_type', 'audio');
            formData.append('props_asset_duration', Math.floor(this.duration));
            formData.append('props_asset_loop', 'false');
            formData.append('props_asset_preload', 'none');
            formData.append('props_asset_autoplay', 'false');
            var fileData = {
              duration: Math.floor(this.duration),
              size: file.size
            };
            var res = validateFile(fileData);
            if (res === true) {
              submitEntity(formData);
            } else {
              showErrors(res);
            }
          }
          var reader = new FileReader();
          reader.onload = (function (aAud) {
            return function (e) {
              aAud.src = e.target.result;
            };
          })(audio);
          reader.readAsDataURL(file);
          break;

        case 'model':
          var reader = new FileReader();
          reader.onload = function (e) {
            validator.validateBytes(new Uint8Array(e.target.result))
              .then((report) => {
                formData.append('type', 'a-gltf-model');
                formData.append('props_asset_type', 'model');
                var fileData = {
                  size: file.size
                };
                var res = validateFile(fileData);
                if (res === true) {
                  submitEntity(formData);
                } else {
                  showErrors(res);
                }
              })
              .catch((error) => {
                console.error('Validation failed: ', error);
              });
          };
          reader.readAsArrayBuffer(file);
          break;
      }

    }



    function validateFile(data) {
      var error = {},
        msgs = [],
        fv = $('#file-validation')[0],
        maxwidth = fv.dataset.maxwidth,
        maxwidthmsg = fv.dataset.maxwidthmsg,
        maxheight = fv.dataset.maxheight,
        maxheightmsg = fv.dataset.maxheightmsg,
        maxsize = fv.dataset.maxsize,
        maxsizemsg = fv.dataset.maxsizemsg,
        maxduration = fv.dataset.maxduration,
        maxdurationmsg = fv.dataset.maxdurationmsg;

      for (var prop in data) {
        switch (prop) {
          case 'width':
            if (data[prop] > maxwidth) msgs.push(maxwidthmsg);
            break;
          case 'height':
            if (data[prop] > maxheight) msgs.push(maxheightmsg);
            break;
          case 'size':
            if (data[prop] > maxsize) msgs.push(maxsizemsg);
            break;
          case 'duration':
            if (data[prop] > maxduration) msgs.push(maxdurationmsg);
            break;
        }
      }
      if (msgs.length === 0) {
        return true;
      } else {
        error = {
          response: {
            data: {
              errors: {}
            }
          }
        };
        error.response.data.errors.messages = msgs;
        return error;
      }
    }



    function submitEntity(formData) {

      // start progress bar
      $('#progress-bar-container')[0].hidden = false;

      axios({
        method: 'post',
        url: '/entities',
        headers: {
          'Content-Type': 'multipart/form-data',
          'Accept': 'application/json'
        },
        data: formData,
        onUploadProgress: function (progressEvent) {
          var percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
          var pb = $('#progress-bar');
          pb.attr('style', 'width: ' + percentCompleted + '%;');
          pb.attr('aria-valuenow', percentCompleted);
          pb.text(percentCompleted + '%');
        },
      }).then(function (response) {

        // finish progress bar
        $('#progress-bar-container')[0].hidden = true;

        // Create Entity Object of Scene and Attach to scene
        var entityObj = createEntityObject(response.data);

        // Create Entity Form and Attach events to Entity Obj
        createEntityForm(entityObj, response.data);

      }).catch(function (error) {
        // finish progress bar
        $('#progress-bar-container')[0].hidden = true;

        //https://github.com/axios/axios#handling-errors
        if (error.response) {
          showErrors(error);
        }
      });
    }



    function createEntityObject(data) {

      var sceneEl = document.getElementById('scene'),
        assetsEl = document.querySelector('a-assets'),
        assetType = data.entity.props.asset.type, // img, video, audio, model
        entityEl = document.createElement(data.entity.type), // a-image, a-video, a-sound, a-gltf-model
        entityProps = data.entity.props.entity;

      // Prepare asset of scene
      // Prevent media autoplay

      assetType = (assetType == 'model') ? 'a-asset-item' : assetType;
      var asset = document.createElement(assetType);
      asset.setAttribute('id', 'asset-' + data.entity.id);
      asset.setAttribute('src', data.entity.props.entity.src);
      if (assetType == 'video' || assetType == 'audio') {
        asset.classList.add('media');
      }
      assetsEl.appendChild(asset);

      // Prepare entity of scene

      for (var prop in entityProps) {
        if (prop == 'position' || prop == 'rotation' || prop == 'scale') {
          var x = parseFloat(entityProps[prop].x),
            y = parseFloat(entityProps[prop].y),
            z = parseFloat(entityProps[prop].z);
          switch (prop) {
            case 'position':
              entityEl.object3D.position.set(x, y + 1.5, z - 1.5);
              break;
            case 'rotation':
              entityEl.object3D.rotation.x = THREE.Math.degToRad(x);
              entityEl.object3D.rotation.y = THREE.Math.degToRad(y);
              entityEl.object3D.rotation.z = THREE.Math.degToRad(z);
              break;
            case 'scale':
              entityEl.object3D.scale.set(x, y, z);
              break;
          }
        } else if (prop == 'src') {
          var src = '#asset-' + data.entity.id;

          // a-sound
          if (data.entity.type == 'a-sound') {
            src = 'src: ' + src;
          }

          entityEl.setAttribute(prop, src);

        } else {
          entityEl.setAttribute(prop, entityProps[prop]);
        }
      }

      // Set animation of model
      if (data.entity.type == 'a-gltf-model') {
        entityEl.setAttribute('animation-mixer', '');
      }

      // Animated GIF config (GIF Shader)

      if (data.entity.type == 'a-image' && data.entity.props.asset.ext == 'gif') {
        entityEl.setAttribute('shader', 'gif');
      }

      if (data.entity.type == 'a-video' || data.entity.type == 'a-sound') {
        setMediaControls();
      }

      entityEl.setAttribute("id", 'entity-obj-' + data.entity.id);
      sceneEl.appendChild(entityEl);
      return entityEl;
    }



    function createEntityForm(entityObj, data) {

      var details,
        entityForm,
        type = data.entity.type,
        entitiesDiv = $('#entities');

      switch (type) {
        case 'a-image':
        case 'a-video':
          details = $('#matrix details:first-child');
          if(type == 'a-image') {
            $('.chromakey', details).hide();
          }
          break;

        case 'a-sound':
          details = $('#matrix details').eq(2);
          break;

        case 'a-gltf-model':
          details = $('#matrix details').eq(1);
          break;
      }

      var newDetails = details.clone();
      newDetails[0].hidden = false;
      newDetails.prependTo(entitiesDiv);

      entityForm = $('#entities form').eq(0);

      // id
      var id = $("input[name='id']", entityForm);
      id.val(data.entity.id);

      // type
      var entityType = $("input[name='type']", entityForm);
      entityType.val(type);

      // scene_id
      var markerId = $("input[name='marker_id']", entityForm);
      markerId.val(data.entity.marker_id);

      // Name
      var name = $("input[name='name']", entityForm);
      name.val(data.entity.name);
      $('summary', entityForm.parent()).text(data.entity.name);
      name.on('input', function () {
        var newName = $(this).val();
        $('summary', entityForm.parent()).text(newName);
      });

      // Width and Height
      var width = $("input[name='entity[width]']", entityForm),
        height = $("input[name='entity[height]']", entityForm),
        ratio = data.entity.props.asset.ratio;

      width.val(entityObj.getAttribute('width'));
      width.on('change', function () {
        var newWidth = $(this).val(),
          newHeight = newWidth / ratio;
        newHeight = parseFloat(Math.round(newHeight * 100) / 100).toFixed(2);
        height.val(newHeight);
        entityObj.setAttribute('width', newWidth);
        entityObj.setAttribute('height', newHeight);
      });

      height.val(entityObj.getAttribute('height'));
      height.on('change', function () {
        var newHeight = $(this).val(),
          newWidth = newHeight * ratio;
        newWidth = parseFloat(Math.round(newWidth * 100) / 100).toFixed(2);
        width.val(newWidth);
        entityObj.setAttribute('height', newHeight);
        entityObj.setAttribute('width', newWidth);
      });

      // position
      var positionX = $("input[name='entity[components][position][x]']", entityForm),
        positionY = $("input[name='entity[components][position][y]']", entityForm),
        positionZ = $("input[name='entity[components][position][z]']", entityForm),
        position = entityObj.getAttribute('position');

      positionX.val(position.x);
      positionX.on('input', function () {
        var x = $(this).val(),
          y = positionY.val(),
          z = positionZ.val();
        entityObj.object3D.position.set(x, y, z);
      });

      positionY.val(position.y);
      positionY.on('input', function () {
        var x = positionX.val(),
          y = $(this).val(),
          z = positionZ.val();
        entityObj.object3D.position.set(x, y, z);
      });

      positionZ.val(position.z);
      positionZ.on('input', function () {
        var x = positionX.val(),
          y = positionY.val(),
          z = $(this).val();
        entityObj.object3D.position.set(x, y, z);
      });

      // rotation
      var rotationX = $("input[name='entity[components][rotation][x]']", entityForm),
        rotationY = $("input[name='entity[components][rotation][y]']", entityForm),
        rotationZ = $("input[name='entity[components][rotation][z]']", entityForm),
        rotation = entityObj.getAttribute('rotation');

      rotationX.val(rotation.x);
      rotationX.on('input', function () {
        var x = $(this).val();
        entityObj.object3D.rotation.x = THREE.Math.degToRad(x);
      });

      rotationY.val(rotation.y);
      rotationY.on('input', function () {
        var y = $(this).val();
        entityObj.object3D.rotation.y = THREE.Math.degToRad(y);
      });

      rotationZ.val(rotation.z);
      rotationZ.on('input', function () {
        var z = $(this).val();
        entityObj.object3D.rotation.z = THREE.Math.degToRad(z);
      });

      // scale
      var scaleX = $("input[name='entity[components][scale][x]']", entityForm),
        scaleY = $("input[name='entity[components][scale][y]']", entityForm),
        scaleZ = $("input[name='entity[components][scale][z]']", entityForm),
        scale = entityObj.getAttribute('scale');

      scaleX.val(scale.x);
      scaleX.on('input', function () {
        var x = $(this).val(),
          y = scaleY.val(),
          z = scaleZ.val();
        entityObj.object3D.scale.set(x, y, z);
      });

      scaleY.val(scale.y);
      scaleY.on('input', function () {
        var x = scaleX.val(),
          y = $(this).val(),
          z = scaleZ.val();
        entityObj.object3D.scale.set(x, y, z);
      });

      scaleZ.val(scale.z);
      scaleZ.on('input', function () {
        var x = scaleX.val(),
          y = scaleY.val(),
          z = $(this).val();
        entityObj.object3D.scale.set(x, y, z);
      });

      // opacity
      var opacity = $("input[name='entity[opacity]']", entityForm),
        defaultOpacity = entityObj.getAttribute('opacity');
      defaultOpacity = defaultOpacity ? defaultOpacity : 1;
      opacity.val(defaultOpacity);
      opacity.on('input', function () {
        var newValue = $(this).val();
        entityObj.setAttribute('opacity', newValue);
      });

      var chromakey = entityObj.getAttribute('chromakey');
      if(chromakey == 1) {
        $('#chromakey').prop('checked', true);
      }


      // a-sound
      // volume
      var loop = $("#inputSoundLoop", entityForm),
        entLoop = entityObj.getAttribute('loop');

      if (entLoop == 'true') {
        loop.prop('checked', true);
        loop.val('true');
      } else {
        loop.prop('checked', false);
        loop.val('false');
      }
      loop.on('change', function () {
        var isChecked = $(this).prop('checked');
        if (isChecked) {
          $(this).val('true');
        } else {
          $(this).val('false');
        }
        entityObj.setAttribute('loop', isChecked);
      });

      // Delete Button
      var btnDelete = $("a:last", entityForm);
      btnDelete.on('click', function (e) {
        var id = $("input[name='id']", entityForm);
        newDetails.hide(200, function () {
          newDetails[0].parentNode.removeChild(newDetails[0]);
        });
        deleteEntity(id.val());
        e.preventDefault();
      });
    }



    function deleteEntity(id) {
      var formData = new FormData();
      formData.append('_method', 'DELETE');
      formData.append('id', id);
      axios({
        method: 'post',
        url: '/entities/' + id,
        headers: {
          'Content-Type': 'multipart/form-data',
          'Accept': 'application/json'
        },
        data: formData,
      }).then(function (response) {
        var entityEl = $('#entity-obj-' + id)[0];
        entityEl.parentNode.removeChild(entityEl);

        var assetEl = $('#asset-' + id)[0];
        assetEl.parentNode.removeChild(assetEl);
        if ($('a-assets .media').length == 0) {
          $('#btn-play-pause').prop('hidden', true);
        }

      }).catch(function (error) {
        if (error.response) {
          showErrors(error);
        }
      });
    }



    function setMarker(marker) {
      $('#marker_id').val(marker.id);
      $('#marker_image').attr('src', marker.image + '?timestamp=' + ((new Date).getTime()));
      $('#marker_url').text(marker.scene_url);

    }



    function saveScene() {

      startSpinner();

      // ---- Scene ---- //
      // title, description, type, status, editable, published_at
      var scene = {
        title: $('#title').val(),
        description: $('#description').val(),
        type: 's', // s - single , b - bundle
        status: $('#status').val(),
        published_at: $('#published_at').val()
      };

      // admin
      var editable = $('#editable');
      if (editable) {
        if (editable.checked) {
          scene.editable = 0;
        } else {
          scene.editable = 1;
        }
      }

      // Publishing
      if (scene.status == 1 && !scene.published_at) {
        var dt = new Date(),
          Y = dt.getFullYear(),
          m = dt.getMonth() + 1,
          d = dt.getDate(),
          H = dt.getHours(),
          i = dt.getMinutes(),
          s = dt.getSeconds();
        if (m < 10) m = `0${m}`;
        if (d < 10) d = `0${d}`;
        if (H < 10) H = `0${H}`;
        if (i < 10) i = `0${i}`;
        if (s < 10) s = `0${s}`;

        scene.published_at = `${Y}-${m}-${d} ${H}:${i}:${s}`;
        $('#published_at').val(scene.published_at);
      }

      scene._method = 'put';

      axios({
        method: 'post',
        url: '/scenes/' + $('#scene_id').val(),
        headers: {
          'Accept': 'application/json'
        },
        data: scene
      }).then(function (response) {

        var _scene = response.data.scene,
          status = $('#info-status'),
          published = $('#info-published');

        switch (_scene.status) {
          case '0':
            status.text(status[0].dataset.draft);
            break;
          case '1':
            status.text(status[0].dataset.published);
            break;
          case '2':
            status.text(status[0].dataset.archived);
            break;
        }

        if (_scene.published_at) {
          var pubDate = _scene.published_at.slice(0, 16);
          published.text(pubDate);
        }

        // ---- Entities ---- //
        var entitiesForm = $('#entities form');
        if (entitiesForm.length) {
          entitiesForm.each(function (index, form) {
            var entityFormData = new FormData(form);
            entityFormData.append('_method', 'PUT');
            if (entityFormData.get("type") != 'a-sound') {
              var positionY = parseFloat(entityFormData.get("entity[components][position][y]")) - 1.5,
                positionZ = parseFloat(entityFormData.get("entity[components][position][z]")) + 1.5;
              entityFormData.set("entity[components][position][y]", positionY);
              entityFormData.set("entity[components][position][z]", positionZ);
            }
            axios({
              method: 'post',
              url: '/entities/' + entityFormData.get('id'),
              headers: {
                'Accept': 'application/json'
              },
              data: entityFormData
            }).then(function (response) {
              endSpinner();
              showSave();
            }).catch(function (error) {
              showErrors(error);
              endSpinner();
            });
          });
        } else {
          endSpinner();
          showSave();
        }

        _saveExtensions();

      }).catch(function (error) {
        showErrors(error);
        endSpinner();
      });

    }




    function showErrors(error) {

      var modal = $('#modalMessage'),
        modalBody = $('#modalMessage .modal-body').first(),
        errors = error.response.data.errors;

      modalBody.text('');

      for (var prop in errors) {
        var arrErrors = errors[prop];
        for (var i = 0; i < arrErrors.length; i++) {
          var msg = $(`<div class="alert alert-danger" role="alert">${arrErrors[i]}</div>`);
          modalBody.append(msg);
        }
      }
      modal.modal('show');
    }



    function setMediaControls() {

      var btnPlayPause = $('#btn-play-pause');
      btnPlayPause.prop('hidden', false);

      btnPlayPause.on('click', function (e) {
        $('a-assets .media').each(function (index, mediaEl) {
          if (mediaEl.paused || mediaEl.ended) {
            mediaEl.play();
          } else {
            mediaEl.pause();
          }
        });
      });
    }



    function startSpinner() {
      $('#save-scene').prop('disabled', true);
      $('#spinner').prop('hidden', false);
    }



    function endSpinner() {
      $('#save-scene').prop('disabled', false);
      $('#spinner').prop('hidden', true);
    }



    function showSave() {
      $('#saved-ok').prop('hidden', false);
      setTimeout(function () {
        $('#saved-ok').hide(300);
      }, 1000);
    }



    ///


  }
})(jQuery);





//--------------------------------------------------//
//            CREATE CUSTOM MARKER PAGE             //
//------------------------------- ------------------//





(function($) {
  "use strict";

  if ($('#create-custom-marker').length) {

    var innerImageURL = null;
    var fullMarkerURL = null;
    var imageName = null;
  
    var btnCustomMarkerFileInput = document.querySelector('#custom_marker_file_input');
    if(btnCustomMarkerFileInput) {
      btnCustomMarkerFileInput.addEventListener('change', function(){
        var file = this.files[0];
        imageName = file.name;
        // remove file extension
        imageName = imageName.substring(0, imageName.lastIndexOf('.')) || imageName;
  
        var reader = new FileReader();
        reader.onload = function(event){
          innerImageURL = event.target.result;
          updateFullMarkerImage();
        };
        reader.readAsDataURL(file);
      });
    }

    function updateFullMarkerImage(){
      // get patternRatio
      var patternRatio = 0.9;
      var imageSize = 1024;
      var borderColor = 'black';

      function hexaColor(color) {
        return /^#[0-9A-F]{6}$/i.test(color);
      };

      var s = new Option().style;
      s.color = borderColor;
        if (borderColor === '' || (s.color != borderColor && !hexaColor(borderColor))) {
        // if color not valid, use black
        borderColor = 'black';
      }

      THREEx.ArPatternFile.buildFullMarker(innerImageURL, patternRatio, imageSize, borderColor, function onComplete(markerUrl){
        fullMarkerURL = markerUrl;

        var fullMarkerImage = document.createElement('img');
        fullMarkerImage.src = fullMarkerURL;
        fullMarkerImage.width = '400';
        fullMarkerImage.classList.add('img-thumbnail');

        // put fullMarkerImage into #imageContainer
        var container = document.querySelector('#custom_marker_image_container');
        while (container.firstChild) container.removeChild(container.firstChild);
        container.appendChild(fullMarkerImage);
        

        document.querySelector('#custom_marker_image').value = fullMarkerURL;

        THREEx.ArPatternFile.encodeImageURL(innerImageURL, function onComplete(patternFileString){
          document.querySelector('#custom_marker_pattern').value = patternFileString;
        });

      });
      

      // Create thumbnail

      var imageSize = 200;

      THREEx.ArPatternFile.buildFullMarker(innerImageURL, patternRatio, imageSize, borderColor, function onComplete(markerUrl){
        document.querySelector('#custom_marker_image_thumb').value = markerUrl;
      });

    }
  }

})(jQuery);





//--------------------------------------------------//
//                      GENERAL                     //
//------------------------------- ------------------//





(function($) {
  "use strict";

  // Logout modal
  $('#btn-logout').on('click', function(e) {
    $('#logout-form')[0].submit();
    e.preventDefault();
  });

  // Delete custom marker
  $('form#form-delete-custom-marker').on('submit', function(e) {
    return confirm(this.dataset.message);
  })

  // Delete scene
  $('#form-delete-scene').on('submit', function(e) {
    return confirm(this.dataset.message);
  });

  // Delete user
  $('form#form-delete-user').on('submit', function(e) {
    return confirm(this.dataset.message);
  });

})(jQuery);





// ================== [EXTENSIONS] ================ //


/** 
 * Handles extensions
*/
function handleExtensions() {
  _loadExtensions();
  _enableChooseExtension();
}



/** 
* Enables modal extension
*/
function _enableChooseExtension() {

  $('#btnChooseExtension').on('click', e => {
      $('#extensionsContainer .extension').each(function(index, elem) {
          let type = $(elem).data('type');
          $(`tr[data-type=${type}]`).addClass('d-none');
      });
      $('#chooseExtensionModal').modal('show');
  });

  _addExtension();
  
}



/** 
* Adds an extension
* ContactBar, Redirection, ContactForm, PopupCallToAction, ButtonCallToAction
*/
function _addExtension() {

  $('.btnAddExtension').on('click', function(e) {
      const type = $(this).parent('td').parent('tr').data('type');
      const detail = $(`details[data-type=${type}]`).clone();
      detail.removeClass('d-none');
      $('#extensionsContainer').append(detail);

      // PopupCallToAction, ButtonCallToAction
      if(detail.data('type') == 'PopupCallToAction' 
          || detail.data('type') == 'ButtonCallToAction') {
          _addEventsToExtensionForm(detail);
      }

      _saveExtension(detail);

      $('button.delete-extension', detail).on('click', function(e) {
          const extensionId = $('input[name=extension_id]', detail).val();
          _deleteExtension(extensionId);
          const type = detail.data('type');
          $(`tr[data-type=${type}]`).removeClass('d-none');
          detail.fadeOut(() => {
              detail.remove();
          });
      });
  });

}



/** 
* Adds events to PopupCallToAction and ButtonCallToAction extensions
*/
function _addEventsToExtensionForm(detail) {

  const btnCallToAction = $('.btn-call-to-action', detail),
        btnInputText = $('input[name=button_text]', detail),
        btnInputBackgroundColor = $('input[name=button_background]', detail),
        btnInputTextColor = $('input[name=button_textcolor]', detail);

  btnInputText.on('input', e => {
      btnCallToAction.text(btnInputText.val());
  });

  // Pallete Settings
  if(!$('style[data-colopicker="css"]').length) {
      $('head').append('<style data-colopicker="css">.holograma-colopicker { border-color: #dddddd }</style>');
  }

  const colorPickerOptions = {
      color: '#333333',
      preferredFormat: "hex",
      containerClassName: 'holograma-colopicker',
      replacerClassName: 'holograma-colopicker',
      allowEmpty: false,
      showAlpha: false,
      showInput: false,
      showButtons: true,
      chooseText: btnInputBackgroundColor.data('choose'),
      cancelText: btnInputBackgroundColor.data('cancel'),
      showPalette: true,
      showSelectionPalette: true,
      palette: [
          ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
          ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
          ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
          ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
          ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
          ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
          ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
          ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
      ],
      clickoutFiresChange: true
  };

  btnInputBackgroundColor.spectrum(colorPickerOptions);
  btnInputBackgroundColor.on('change', function(e) {
      btnCallToAction.css({ 'background-color': btnInputBackgroundColor.val()});
  });

  btnInputTextColor.spectrum(colorPickerOptions);
  btnInputTextColor.on('change', function(e) {
      btnCallToAction.css({ 'color': btnInputTextColor.val()});
  });
  
}



/** 
* Saves an extension
*/
function _saveExtension(detail) {

  const extensionType = detail.data('type');
  let formData, url;

  switch (extensionType) {
      case 'ContactBar':
          formData = _getContactBarFormData(detail);
          break;
      case 'ButtonCallToAction':
          formData = _getButtonCallToActionFormData(detail);
          break;
  }

  url = '/extensions';
  if(formData.id) {
      url += '/' + formData.id;
      formData._method = 'PUT';
  }

  formData.scene_id = $('input[name=scene_id]').val();

  axios({
      url: url,
      method: 'POST',
      headers: { 'Accept': 'application/json' },
      data: formData
  }).then(response => {
      $('input[name=extension_id]', detail).val(response.data.id);
  }).catch(error => {
      showErrors(error);
  });

}



function _getContactBarFormData(detail) {

  const formData = {props: {}};

  if($('input[name=extension_id]', detail).val()) {
      formData.id = $('input[name=extension_id]', detail).val();
  }

  formData.type = 'ContactBar';
  formData.props.whatsapp_number = $('input[name=whatsapp_number]', detail).val();
  formData.props.phone_number = $('input[name=phone_number]', detail).val();
  formData.props.telegram_url = $('input[name=telegram_url]', detail).val();
  formData.props.messenger_url = $('input[name=messenger_url]', detail).val();
  formData.props.facebook_url = $('input[name=facebook_url]', detail).val();
  formData.props.instagram_url = $('input[name=instagram_url]', detail).val();
  formData.props.twitter_url = $('input[name=twitter_url]', detail).val();
  formData.props.github_url = $('input[name=github_url]', detail).val();
  formData.props.linkedin_url = $('input[name=linkedin_url]', detail).val();
  formData.props.youtube_url = $('input[name=youtube_url]', detail).val();
  formData.props.site_url = $('input[name=site_url]', detail).val();
  formData.props.email = $('input[name=email]', detail).val();

  formData.props = JSON.stringify(formData.props);

  return formData;
}



/** 
* [ADMIN/CREATOR]
*/
function _getButtonCallToActionFormData(detail) {

  const formData = {props: {}};

  if($('input[name=extension_id]', detail).val()) {
      formData.id = $('input[name=extension_id]', detail).val();
  }

  formData.type = 'ButtonCallToAction';
  formData.props.button_text = $('input[name=button_text]', detail).val();
  formData.props.button_background = $('input[name=button_background]', detail).val();
  formData.props.button_textcolor = $('input[name=button_textcolor]', detail).val();
  formData.props.button_link = $('input[name=button_link]', detail).val();

  formData.props = JSON.stringify(formData.props);

  return formData;

}



/** 
* [ADMIN/CREATOR]
* Deletes an extension
*/
function _deleteExtension(extensionId) {

  const url = `/extensions/${extensionId}`;

  axios({
      url: url,
      method: 'POST',
      headers: { 'Accept': 'application/json' },
      data: { _method: 'DELETE' }
  })
  .then(response => {})
  .catch(error => {
      showErrors(error);
  });

}



/** 
* [ADMIN/CREATOR]
* Loads all extensions on page load
*/
function _loadExtensions() {

  const scene_id = $('input[name=scene_id]').val(),
        url = `/extensions/scene/${scene_id}`;

  axios({
      url: url,
      headers: { 'Accept': 'application/json' },
  })
  .then(response => {

      const extensions = response.data;
      extensions.forEach(extension => {

          const type = extension.type;

          $(`#extensionItems tr[data-type=${type}]`).addClass('d-none');

          const detail = $(`details[data-type=${type}]`).clone();
          detail.removeClass('d-none');
          $('#extensionsContainer').append(detail);

          // PopupCallToAction, ButtonCallToAction
          if(detail.data('type') == 'PopupCallToAction' 
              || detail.data('type') == 'ButtonCallToAction') {
              _addEventsToExtensionForm(detail);
          }

          $('button.delete-extension', detail).on('click', function(e) {
              const extensionId = $('input[name=extension_id]', detail).val();
              _deleteExtension(extensionId);
              const type = detail.data('type');
              $(`tr[data-type=${type}]`).removeClass('d-none');
              detail.fadeOut(() => {
                  detail.remove();
              });
          });

          _setExtensionValues(extension, detail);
      });

  });
}



/** 
* Sets values of extension form
*/
function _setExtensionValues(extension, detail) {
  extension.props = JSON.parse(extension.props);
  switch (extension.type) {
      case 'ContactBar':
          _setContactBarValues(extension, detail);
          break;
      case 'ButtonCallToAction':
          _setButtonCallToActionValues(extension, detail);
          break;
  }
}



function _setContactBarValues(extension, detail) {
  $('input[name=phone_number]', detail).val(extension.props.phone_number);
  $('input[name=whatsapp_number]', detail).val(extension.props.whatsapp_number);
  $('input[name=telegram_url]', detail).val(extension.props.telegram_url);
  $('input[name=messenger_url]', detail).val(extension.props.messenger_url);
  $('input[name=facebook_url]', detail).val(extension.props.facebook_url);
  $('input[name=instagram_url]', detail).val(extension.props.instagram_url);
  $('input[name=twitter_url]', detail).val(extension.props.twitter_url);
  $('input[name=github_url]', detail).val(extension.props.github_url);
  $('input[name=linkedin_url]', detail).val(extension.props.linkedin_url);
  $('input[name=youtube_url]', detail).val(extension.props.youtube_url);
  $('input[name=site_url]', detail).val(extension.props.site_url);
  $('input[name=email]', detail).val(extension.props.email);
  $('input[name=extension_id]', detail).val(extension.id);
}



function _setButtonCallToActionValues(extension, detail) {
  $('input[name=button_text]', detail).val(extension.props.button_text);
  $('input[name=button_textcolor]', detail).spectrum('set', extension.props.button_textcolor);
  $('input[name=button_background]', detail).spectrum('set', extension.props.button_background);
  $('input[name=button_link]', detail).val(extension.props.button_link);
  $('input[name=extension_id]', detail).val(extension.id);
  $('button', detail).eq(0).text(extension.props.button_text);
  $('button', detail).eq(0).css({'background-color': extension.props.button_background, 'color': extension.props.button_textcolor});
}



/** 
* Saves all extensions
*/
function _saveExtensions() {
  const details = $('#extensionsContainer details');
  
  details.each((index, detail) => {
      _saveExtension($(detail));
  });
}