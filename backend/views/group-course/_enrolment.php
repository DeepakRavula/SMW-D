<div class="row">
    <div class="col-xs-5">
        <select name="from[]" id="undo_redo" class="form-control" size="13" multiple="multiple">
            <option value="1">C++</option>
            <option value="2">C#</option>
            <option value="3">Haskell</option>
            <option value="4">Java</option>
            <option value="5">JavaScript</option>
            <option value="6">Lisp</option>
            <option value="7">Lua</option>
            <option value="8">MATLAB</option>
            <option value="9">NewLISP</option>
            <option value="10">PHP</option>
            <option value="11">Perl</option>
            <option value="12">SQL</option>
            <option value="13">Unix shell</option>
        </select>
    </div>
    
    <div class="col-xs-2">
        <button type="button" id="undo_redo_undo" class="btn btn-primary btn-block">undo</button>
        <button type="button" id="undo_redo_rightAll" class="btn btn-default btn-block"><i class="glyphicon glyphicon-forward"></i></button>
        <button type="button" id="undo_redo_rightSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
        <button type="button" id="undo_redo_leftSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
        <button type="button" id="undo_redo_leftAll" class="btn btn-default btn-block"><i class="glyphicon glyphicon-backward"></i></button>
        <button type="button" id="undo_redo_redo" class="btn btn-warning btn-block">redo</button>
    </div>
    
    <div class="col-xs-5">
        <select name="to[]" id="undo_redo_to" class="form-control" size="13" multiple="multiple"></select>
    </div>
</div>