/**
 *
 * Created by bricks on 16/5/4.
 */

var crop_image = {
    handleFiles: function () {
        console.log(this.files);
        var fileList = this.files[0];
        var oFReader = new FileReader();
        oFReader.readAsDataURL(fileList);
        oFReader.onload = function (oFREvent) {
            crop_image.paintImage(oFREvent.target.result);
        };
    },

    paintImage : function(url){
        var createCavans = dom.getImage.getContext("2d");
        var img = new Image();
        img.src = url;

        img.onload = function(){
            if ( img.width < dom.regional.offsetWidth && img.height < dom.regional.offsetHeight) {
                coordianteAndDistanceInfo.imageWidth = img.width;
                coordianteAndDistanceInfo.imgHeight = img.height;

            } else {
                var pWidth = img.width / (img.height / dom.regional.offsetHeight);
                var pHeight = img.height / (img.width / dom.regional.offsetWidth);
                coordianteAndDistanceInfo.imgWidth = img.width > img.height ? dom.regional.offsetWidth : pWidth;
                coordianteAndDistanceInfo.imgHeight = img.height > img.width ? dom.regional.offsetHeight : pHeight;
            }

            coordianteAndDistanceInfo.px = (dom.regional.offsetWidth - coordianteAndDistanceInfo.imgWidth) / 2 + 'px';
            coordianteAndDistanceInfo.py = (dom.regional.offsetHeight - coordianteAndDistanceInfo.imgHeight) / 2 + 'px';

            dom.getImage.height = coordianteAndDistanceInfo.imgHeight;
            dom.getImage.width = coordianteAndDistanceInfo.imgWidth;
            dom.getImage.style.left = coordianteAndDistanceInfo.px;
            dom.getImage.style.top = coordianteAndDistanceInfo.py;

            createCavans.drawImage(img,0,0,
                coordianteAndDistanceInfo.imgWidth,coordianteAndDistanceInfo.imgHeight);
            //cutImage.cutImage(dom.getImage.toDataURL());
            //cutImage.drag();
        };

    },
};

