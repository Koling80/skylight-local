<div class="content">
    <div class="about-container">

        <div class="feedback_form">

            <h1>Feedback</h1>

            <p>Please contact us with your suggestions or questions below.</p>

            <?php echo validation_errors(); ?>

           <!-- <script type="text/javascript">
                var RecaptchaOptions = {
                    theme : 'clean'
                };
            </script>-->

            <?php //echo form_open($form_prefix.'feedback'); ?>
            <label for="name" id="labelName">Name:</label> <input type="text" id="name" name="name" size="50" value="<?php echo set_value('name'); ?>" /><br />
            <label for="email" id="labelEmail">Email:</label> <input type="text" id="email" name="email" size="50" value="<?php echo set_value('email'); ?>" /><br />
            <label for="feedback" id="labelFeedback">Message:</label> <textarea type="text" id="feedback" name="feedback" rows="15" cols="80" /><?php echo set_value('feedback'); ?></textarea><br />

            <p>&nbsp;</p>

            <!--<p>Please enter the following verification words into the box:</p>--> <?php
            //echo recaptcha_get_html($recaptcha_key_public);
            ?>

            <p>&nbsp;</p>

            <input type="submit" class="btn" value="Send" />
            </form>
        </div>
    </div>
</div>