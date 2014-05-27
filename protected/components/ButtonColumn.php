<?php
/**
 * ButtonColumn class file.
 * Extends {@link CButtonColumn}
 * 
 * Allows additional evaluation of ID in options.
 * 
 * @version $Id$
 * 
 */
class ButtonColumn extends CButtonColumn
{
    /**
     * @var boolean whether the ID in the button options should be evaluated.
     */
    public $evaluateID = false;
 
    /**
     * Renders the button cell content.
     * This method renders the view, update and delete buttons in the data cell.
     * Overrides the method 'renderDataCellContent()' of the class CButtonColumn
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    public function renderDataCellContent($row, $data)
    {
        $tr=array();
        ob_start();
        foreach($this->buttons as $id=>$button)
        {
            if($this->evaluateID) 
            {
                foreach ($button['options'] as $key=>$options) {
                    if(substr( $options, 0, 5 ) === "php->")
                        $button['options'][$key] = $this->evaluateExpression(substr( $options, 5), array('row'=>$row,'data'=>$data));
                }
            }
 
            $this->renderButton($id,$button,$row,$data);
            $tr['{'.$id.'}']=ob_get_contents();
            ob_clean();
        }
        ob_end_clean();
        echo strtr($this->template,$tr);
    }
}