<?php
namespace AdvancedAllInOneForms;
if (!defined( 'ABSPATH')) exit; 
if (!class_exists( 'AAIOF_Frontform')){
    class AAIOF_Frontform {
        public function aaiof_front_design_view($attr){
            foreach($attr as $ky=>$vl){ $attr[$ky] = esc_html($vl);}
            $vcf_data = get_post_meta( $attr['id'], 'vcf_fields_data', true);
            $get_vcf_data = unserialize($vcf_data);
            _e('<form action="" method="post" id="vcf7form-'.$attr['id'].'" class="vcf7form" data-id="'.$attr['id'].'" enctype="multipart/form-data">');
            foreach($get_vcf_data as $k=>$data){
                $type = $data['type'];
                if($type!='display'){
                    $this->$type($k,$data,$attr);
                }
            }
            _e('</form>');

            ?>
            <script>
            function aaiof_init_events(){
                jQuery('.ffield').each(function(){
                    var id_value = jQuery(this).attr('id');
                    var id_value2 = id_value.split('-');
                    if(id_value2.length>2){
                        console.log('C:'+id_value2[1]+'-'+id_value2[2]);
                    }else{
                        console.log('C:'+id_value2[1]);
                    }
                });
                jQuery('.dfield').each(function(){
                    var condid = jQuery(this).attr('id');
                    var condids = new Array();
                    var condfield = jQuery(this).attr('condfield');
                    var condmatch = jQuery(this).attr('condmatch');
                    var condvalue = jQuery(this).attr('condvalue');
                    if(typeof(condfield)!=="undefined" && typeof(condmatch)!=="undefined" && typeof(condvalue)!=="undefined"){
                        /*console.log('D: '+condfield+' '+condmatch+' '+condvalue);*/                        
                        jQuery('.ffield').each(function(){
                            var id_value = jQuery(this).attr('id');
                            var id_value2 = id_value.split('-');
                            if(id_value2.length>2){
                                /*console.log('C:'+id_value2[1]+'-'+id_value2[2]);*/
                            }else{
                                /*console.log('C:'+id_value2[1]);*/
                            }
                            if((id_value2[0]=='text' || id_value2[0]=='email') && id_value2[1]==condfield){
                                condids.push(condid);
                                jQuery('#'+condid).hide();
                                /*console.log('match:'+condids);*/
                                jQuery('#'+id_value).unbind();
                                jQuery('#'+id_value).on("keyup change", function(e) {
                                    if(jQuery(this).val()==condvalue){
                                        for(k=0;k<condids.length;k++){
                                            jQuery('#'+condids[k]).show();
                                        }
                                    }else{
                                        for(k=0;k<condids.length;k++){
                                            jQuery('#'+condids[k]).hide();
                                        }
                                    }
                                });
                            }
                            else if(id_value2[0]=='radio' && id_value2[1]==condfield){
                                condids.push(condid);
                                jQuery('#'+condid).hide();
                                /*console.log('match:'+condids);*/
                                jQuery('#'+id_value).unbind();
                                jQuery('#'+id_value).on("change", function(e) {
                                    if(jQuery(this).val()==condvalue){
                                        for(k=0;k<condids.length;k++){
                                            jQuery('#'+condids[k]).show();
                                        }
                                    }else{
                                        for(k=0;k<condids.length;k++){
                                            jQuery('#'+condids[k]).hide();
                                        }
                                    }
                                });
                            }
                        });
                    }
                });
            }   
            aaiof_init_events();             
            </script>                
            <?php
        }
        public function text($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }

            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }
            
            if($data['max'] == '' && $data['min'] == ''){
                _e($raws);                
                _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
                _e('<div class="form-group" id="'.$data['id'].'">
                <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
                <input type="'.$data['type'].'" class="ffield '.$k.' form-control '.$data['class'].'" id="'.$data['type'].'-'.$k.'" placeholder="'.$data['placeholder'].'" name="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' '.$f_param.'>
                </div></div>');
                _e($rawed);
            }else{
                _e($raws);
                _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
                _e('<div class="form-group" id="'.$data['id'].'">
                <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
                <input type="'.$data['type'].'" class="ffield '.$k.' form-control '.$data['class'].'" id="'.$data['type'].'-'.$k.'" placeholder="'.$data['placeholder'].'" name="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' maxlength = "'.$data['max'].'" minlength = "'.$data['min'].'" '.$f_param.'>
                </div></div>');
                _e($rawed);
            }
        }
        public function description($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }

            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }

            _e($raws);            
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            _e('<div class="form-group" id="'.$data['id'].'">');
                if($data['placeholder'] == ''){
                    _e('<p>'.$data['label'].'</p>');
                }else{
                    _e('<'.$data['placeholder'].'>'.$data['label'].'</'.$data['placeholder'].'>');
                }                    
            _e('</div>');
            _e('</div>');
            _e($rawed);
        }
        public function rating($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }
            _e($raws);

            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }

            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            _e('<div class="form-group"><div><p for="'.$data['type'].'-'.$k.'">'.$data['label'].'</p>');
            _e('<div class="form-group rating '.$data['class'].'" id="'.$data['id'].'">');
            _e('<label>
                    <input class="ffield" type="radio" name="'.$data['type'].'-'.$k.'" value="1" '.(($data['required']=='yes')?'required="required"':"").' '.$f_param.'/>
                    <span class="icon">★</span>
                    </label>
                    <label>
                    <input class="ffield" type="radio" name="'.$data['type'].'-'.$k.'" value="2" '.$f_param.'/>
                    <span class="icon">★</span>
                    <span class="icon">★</span>
                    </label>
                    <label>
                    <input class="ffield" type="radio" name="'.$data['type'].'-'.$k.'" value="3" '.$f_param.'/>
                    <span class="icon">★</span>
                    <span class="icon">★</span>
                    <span class="icon">★</span>   
                    </label>
                    <label>
                    <input class="ffield" type="radio" name="'.$data['type'].'-'.$k.'" value="4" '.$f_param.'/>
                    <span class="icon">★</span>
                    <span class="icon">★</span>
                    <span class="icon">★</span>
                    <span class="icon">★</span>
                    </label>
                    <label>
                    <input class="ffield" type="radio" name="'.$data['type'].'-'.$k.'" value="5" '.$f_param.'/>
                    <span class="icon">★</span>
                    <span class="icon">★</span>
                    <span class="icon">★</span>
                    <span class="icon">★</span>
                    <span class="icon">★</span>
                    </label>');                    
            _e('</div></div></div>');
            _e('</div>');
            _e($rawed);
        }
        public function password($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }

            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }

            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            if($data['max'] == 'yes'){   
                if($data['min'] == ''){
                    _e('<div class="form-group" id="'.$data['id'].'">
                    <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'  <span> (Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters)</span></label>
                    <input type="password" class="ffield '.$k.' form-control '.$data['class'].'" id="'.$data['type'].'-'.$k.'" placeholder="'.$data['placeholder'].'" name="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" '.$f_param.'>
                    </div>');
                }else{
                    _e('<div class="form-group" id="'.$data['id'].'">
                    <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'  <span> (Must contain at least one number and one uppercase and lowercase letter, and at least '.$data['min'].' or more characters)</span></label>
                    <input type="password" class="ffield '.$k.' form-control '.$data['class'].'" id="'.$data['type'].'-'.$k.'" placeholder="'.$data['placeholder'].'" name="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{'.$data['min'].',}" '.$f_param.'>
                    </div>');
                }
            }else{
                _e('<div class="form-group" id="'.$data['id'].'">
                <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
                <input type="password" class="ffield '.$k.' form-control '.$data['class'].'" id="'.$data['type'].'-'.$k.'" placeholder="'.$data['placeholder'].'" name="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' minlength="'.$data['min'].'" '.$f_param.'>
                </div>');
            }
            _e('</div>');
            _e($rawed);
        }
        public function email($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }

            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }

            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            _e('<div class="form-group" id="'.$data['id'].'">
            <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
            <input type="email" class="ffield '.$k.' form-control '.$data['class'].'" id="'.$data['type'].'-'.$k.'" placeholder="'.$data['placeholder'].'" name="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,63}$" '.$f_param.'>
            </div>');
            _e('</div>');
            _e($rawed);
        }
        public function phone($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }

            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }

            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            if($data['max'] == '' && $data['min'] == ''){
                _e('<div class="form-group" id="'.$data['id'].'">
                <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
                <input type="tel" class="ffield '.$k.' form-control '.$data['class'].'" id="'.$data['type'].'-'.$k.'" placeholder="'.$data['placeholder'].'" name="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' '.$f_param.'>
                </div>');
            }else{
                _e('<div class="form-group" id="'.$data['id'].'">
                <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
                <input type="tel" class="ffield '.$k.' form-control '.$data['class'].'" id="'.$data['type'].'-'.$k.'" placeholder="'.$data['placeholder'].'" name="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' maxlength = "'.$data['max'].'" minlength = "'.$data['min'].'" '.$f_param.'>
                </div>');
            }
            _e('</div>');
            _e($rawed);
        }
        public function textarea($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes'){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }

            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }

            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            if($data['max'] == ''){
                _e('<div class="form-group" id="'.$data['id'].'">
                <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
                <textarea name="'.$data['type'].'-'.$k.'" class="ffield '.$k.' form-control '.$data['class'].'" id="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' rows="'.$data['rows'].'" cols="'.$data['columns'].'" '.$f_param.'></textarea>
                </div>');
            }else{
                _e('<div class="form-group" id="'.$data['id'].'">
                <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
                <textarea name="'.$data['type'].'-'.$k.'" class="ffield '.$k.' form-control '.$data['class'].'" id="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' rows="'.$data['rows'].'" cols="'.$data['columns'].'" maxlength="'.$data['max'].'" '.$f_param.'></textarea>
                </div>');
            }
            _e('</div>');
            _e($rawed);
        }
        public function url($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }

            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }

            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            _e('<div class="form-group" id="'.$data['id'].'">
            <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
            <input type="url" class="ffield '.$k.' form-control '.$data['class'].'" id="'.$data['type'].'-'.$k.'" placeholder="'.$data['placeholder'].'" name="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' '.$f_param.'>
            </div>');
            _e('</div>');
            _e($rawed);
        }
        public function date($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }

            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }

            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            _e('<div class="form-group" id="'.$data['id'].'">
            <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
            <input type="text" class="ffield '.$k.' form-control datepicker '.$data['class'].'" id="'.$data['type'].'-'.$k.'"  placeholder="MM/DD/YYYY" name="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' '.$f_param.'>
            </div>');
            _e('</div>');
            _e($rawed);
        }
        public function file($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }

            $f_cond = '';
            $f_param = ''; 
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }

            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            _e('<div class="form-group '.$data['class'].' add_file" id="'.$data['id'].'">
            <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
            <input type="file" class="ffield '.$k.' form-control '.$data['class'].'" id="'.$data['type'].'-'.$k.'"  name="'.$data['type'].'-'.$k.'" accepted="'.$data['extension'].'" data-extension="'.$data['extension'].'" data-filesize="'.$data['filesize'].'" '.(($data['required']=='yes')?'required="required"':"").' '.$f_param.'>
            <label id="file_error" class="error"></label>
            </div>');
            _e('</div>');
            _e($rawed);
        }
        public function time($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }

            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }

            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            _e('<div class="form-group" id="'.$data['id'].'">
            <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
            <input type="text" class="ffield '.$k.' form-control timepicker '.$data['class'].'" id="'.$data['type'].'-'.$k.'" placeholder="HH:MM" name="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' '.$f_param.' readonly>
            </div>');
            _e('</div>');
            _e($rawed);
        }
        public function select($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }
            
            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }
            
            $html = '<div class="form-group" id="'.$data['id'].'">
                    <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
                    <select name="'.$data['type'].'-'.$k.'" class="ffield '.$k.' form-control '.$data['class'].'" id="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' '.$f_param.'>
                        <option value="">'.$data['placeholder'].'</option>';
                        foreach($data['option'] as $key=>$value){
                            if($value != ''){   
                                if($data['option_val'][$key] != '')
                                {
                                    $html .= '<option value="'.$data['option_val'][$key].'">'.$value.'</option>';
                                }
                                else
                                {
                                    $html .= '<option value="'.$value.'">'.$value.'</option>';
                                }
                            }
                        }
            $html .= '</select>
            </div>';
            
            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            _e($html);
            _e('</div>');
            _e($rawed);
        }
        public function radio($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }
            
            $f_cond = '';
            $f_param = ''; 
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }

            $html = '<div class="form-group radio_option '.$data['class'].'" id="'.$data['id'].'">
            <label>'.$data['label'].'</label><div>';
            foreach($data['option'] as $key=>$value){
                if($value != ''){
                    $html .= '<div class="checkbox">
                    <label for="'.$data['type'].'-'.$k.'-'.$key.'"><input type="'.$data['type'].'" value="'.$data['option_val'][$key].'" name="'.$data['type'].'-'.$k.'" class="ffield" id="'.$data['type'].'-'.$k.'-'.$key.'" '.(($data['required']=='yes')?'required="required"':"").' '.$f_param.'><span>'.$value.'</span></label>
                    </div>';
                }
            }
            $html .= '</div></div>';

            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            _e($html);
            _e('</div>');
            _e($rawed);
        }
        public function checkbox($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }        
            
            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }

            $html = '<div class="form-group checkbox_option '.$data['class'].'" id="'.$data['id'].'" '.$f_cond.$f_param.'>
            <label >'.$data['label'].'</label><div>';
            foreach($data['option'] as $key=>$value){
                if($value != ''){
                    $html .= '<div class="checkbox">
                    <label for="'.$data['type'].'-'.$k.'-'.$key.'"><input type="'.$data['type'].'" value="'.$data['option_val'][$key].'" name="'.$data['type'].'-'.$k.'[]" class="ffield" id="'.$data['type'].'-'.$k.'-'.$key.'" '.(($data['required']=='yes')?'required="required"':"").' '.$f_param.'><span>'.$value.'</span></label>
                    </div>';
                }
            }
            $html .= '</div></div>';

            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            _e($html);
            _e('</div>');
            _e($rawed);
        }
        public function product_title($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            $show_enquiry_cart_page = get_option('show_enquiry_cart_page');
            if($show_enquiry_cart_page == 1 && is_cart()){
                global $woocommerce;
                $items = $woocommerce->cart->get_cart();
                $count = 1;
                foreach($items as $item => $values){
                    $_product =  wc_get_product( $values['data']->get_id());
                    _e('<input type="hidden" class="form-control '.$data['class'].'" name="'.$data['type'].'-'.$k.'[]" value="'.$_product->get_title().'" readonly>');        
                    $count++;
                }
            }else{
                if($data['raws'] == 'yes' ){
                    $raws = '<div class="row '.$data['rw-cls'].'">';
                }else{
                    $raws = '';
                }
                if($data['rawed'] == 'yes' ){
                    $rawed = '</div>';
                }else{
                    $rawed = '';
                }
                _e($raws);
                _e('<div class="col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'">');
                _e('<div class="form-group">
                <label>'.$data['label'].'</label>
                <p>'.get_the_title().'</p>
                <input type="hidden" class="form-control '.$data['class'].'" name="'.$data['type'].'-'.$k.'" value="'.get_the_title().'" readonly>
                </div>');
                _e('</div>');
                _e($rawed);
            }
        }
        public function product_url($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            $show_enquiry_cart_page = get_option('show_enquiry_cart_page');
            if($show_enquiry_cart_page == 1 && is_cart()){
                global $woocommerce;
                $items = $woocommerce->cart->get_cart();
                $count = 1;
                foreach($items as $item => $values){
                    $_product =  wc_get_product( $values['data']->get_id());
                    _e('<input type="hidden" class="form-control '.$data['class'].'" name="'.$data['type'].'-'.$k.'[]" value="'.$_product->get_permalink().'" readonly>');
                    $count++;
                }
            }else{         
                if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
                    $url = "https://";
                }else{
                    $url = "http://";
                }            
                $url.= sanitize_url(esc_url($_SERVER['HTTP_HOST']));
                $url.= sanitize_url(esc_url($_SERVER['REQUEST_URI']));
                if($data['raws'] == 'yes' ){
                    $raws = '<div class="row '.$data['rw-cls'].'">';
                }else{
                    $raws = '';
                }
                if($data['rawed'] == 'yes' ){
                    $rawed = '</div>';
                }else{
                    $rawed = '';
                }
                _e($raws);
                _e('<div class="col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'">');
                    _e('<div class="form-group">
                            <label>'.$data['label'].'</label>
                            <p><a href="">'.$url.'</a></p>
                            <input type="hidden" class="form-control '.$data['class'].'" name="'.$data['type'].'-'.$k.'" value="'.$url.'" readonly>
                        </div>');
                _e('</div>');
                _e($rawed);
            }
        }
        public function product_price($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            $show_enquiry_cart_page = get_option('show_enquiry_cart_page');
            if($show_enquiry_cart_page == 1 && is_cart()){
                global $woocommerce;
                $items = $woocommerce->cart->get_cart();
                $count = 1;
                foreach($items as $item => $values){
                    $_product =  wc_get_product( $values['data']->get_id());
                    _e('<input type="hidden" class="form-control '.$data['class'].'" name="'.$data['type'].'-'.$k.'[]" value="'.$_product->get_price().'" readonly>');
                    $count++;
                }
            }else{
                $product_id = get_the_ID();
                $_product = wc_get_product( $product_id );
                $regular_price = $_product->get_regular_price();
                $sale_price = $_product->get_sale_price();
                if($sale_price == ''){
                    $p_price = $regular_price;
                }else{
                    $p_price = $sale_price;
                }
                if($data['raws'] == 'yes' ){
                    $raws = '<div class="row '.$data['rw-cls'].'">';
                }else{
                    $raws = '';
                }
                if($data['rawed'] == 'yes' ){
                    $rawed = '</div>';
                }else{
                    $rawed = '';
                }

                _e($raws);
                _e('<div class="col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'">');
                _e('<div class="form-group">
                <label>'.$data['label'].'</label>
                <p>'.wc_price($p_price).'</p>
                <input type="hidden" class="form-control '.$data['class'].'" name="'.$data['type'].'-'.$k.'" value="'.$p_price.'" readonly>
                </div>');
                _e('</div>');
                _e($rawed);
            }
        }
        public function product_qty($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            $show_enquiry_cart_page = get_option('show_enquiry_cart_page');
            if($show_enquiry_cart_page == 1 && is_cart()){
                global $woocommerce;
                $items = $woocommerce->cart->get_cart();
                $count = 1;
                foreach($items as $item => $values){
                    $quantity = $values['quantity'];
                    _e('<input type="hidden" class="form-control '.$data['class'].'" name="'.$data['type'].'-'.$k.'[]" value="'.$quantity.'" readonly>');
                    $count++;
                }
            }else{
                if($data['raws'] == 'yes' ){
                    $raws = '<div class="row '.$data['rw-cls'].'">';
                }else{
                    $raws = '';
                }
                if($data['rawed'] == 'yes' ){
                    $rawed = '</div>';
                }else{
                    $rawed = '';
                }
                _e($raws);
                _e('<div class="col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'">');
                    _e('<div class="form-group">
                        <label>'.$data['label'].'</label>
                        <input type="number" placeholder="Quantity" class="form-control '.$data['class'].'" name="'.$data['type'].'-'.$k.'" value="">
                    </div>');
                _e('</div>');
                _e($rawed);
            }
        }
        public function submit($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }

            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }

            $f_cond = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'" ';
            }

            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            $message = unserialize(get_post_meta( $attr['id'], 'vcf_success_sms', true));
            $thankyou = (isset($message['thankyou']) && trim($message['thankyou'])!="")?$message['thankyou']:home_url();
            _e('<button type="submit" class="ffield '.$k.' btn btn-default  '.$data['class'].'" id="'.$data['type'].'-'.$k.'" '.$f_param.'>'.$data['label'].'</button>');
            _e('<img src="'.AAIOF_ADVANCE_FORM_URL.'assets/images/loading.gif" class="loader_gif" />');
            _e('<div class="form-group success-error" data-url="'.$thankyou.'">'.$message['success'].'</div>');
            _e('</div>');
            _e($rawed);        
        }
        public function recaptcha($k,$data,$attr){
            wp_register_script('recaptcha', 'https://www.google.com/recaptcha/api.js');
            wp_enqueue_script('recaptcha'); 

            foreach($attr as $ky=>$vl){ $attr[$ky] = esc_html($vl);}
            foreach($data as $ky=>$vl){ $data[$ky] = esc_html($vl);}
            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }

            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }

            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            $sitekey = get_option('gcaptcha_sitekey');
            $secretkey = get_option('gcaptcha_secret');
            _e('<div class="form-group" id="'.$data['id'].'">
                <label for="'.$data['type'].'-'.$k.'">'.$data['label'].'</label>
                <div class="g-recaptcha '.$data['class'].'" id="'.$data['id'].'" data-sitekey="'.$sitekey.'" data-callback="recaptchaCallback"></div><input type="hidden" class="ffield '.$k.' hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha" '.$f_param.'>
            </div>');
            _e('<div class="form-group success-error-captcha">Your Captcha response was incorrect. Please try again.</div>');
            _e('</div>');
            _e($rawed);
        }
        public function acceptance($k,$data,$attr){
            foreach($attr as $ky=>$vl){ if(!is_array($vl)){ $attr[$ky] = esc_html($vl); }else{ $attr[$ky] = $vl; } }
            foreach($data as $ky=>$vl){ if(!is_array($vl)){ $data[$ky] = esc_html($vl); }else{ $data[$ky] = $vl; } }
            
            if($data['raws'] == 'yes' ){
                $raws = '<div class="row '.$data['rw-cls'].'">';
            }else{
                $raws = '';
            }
            if($data['rawed'] == 'yes' ){
                $rawed = '</div>';
            }else{
                $rawed = '';
            }

            $f_cond = '';
            $f_param = '';
            if(isset($data['cond']) && 
            trim($data['cond'])=='show' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:none;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }else if(isset($data['cond']) && 
            trim($data['cond'])=='hide' && 
            isset($data['condfield']) && 
            trim($data['condfield'])!="" && 
            isset($data['condmatch']) && 
            isset($data['condvalue'])){
                $f_cond = ' style="display:block;" ';
                $f_param = ' condfield="'.$data['condfield'].'" condmatch="'.$data['condmatch'].'" condvalue="'.$data['condvalue'].'"  ';
            }

            _e($raws);
            _e('<div id="dfield-'.$k.'" class="dfield col-'.$data['col-data'].'-'.$data['col-data-num'].' '.$data['cl-cls'].'" '.$f_cond.$f_param.'>');
            _e('<div class="form-group" id="'.$data['id'].'">
            <div>
            <div class="checkbox">
                <label for="'.$data['type'].'-'.$k.'"><input class="ffield" type="checkbox" value="Yes" name="'.$data['type'].'-'.$k.'" id="'.$data['type'].'-'.$k.'" '.(($data['required']=='yes')?'required="required"':"").' '.$f_param.'><span>'.$data['label'].'</span></label>
            </div>
            </div>
            </div>');
            _e('</div>');
            _e($rawed);
        }
    }
}