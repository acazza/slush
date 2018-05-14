<?php $lastquiz = get_last_quiz();?>

<?php if( $lastquiz !== false ): ?>

    <div class="quiz-page">

        <h3> Here is your Latest Quiz Sumission Result: </h3>

        <?php $postmeta = get_post_meta( $lastquiz[0]->ID, '_repeater', false ); ?>

        <table class="quiz-results zebra">
            <tr>
                <th>#</th>
                <th>Question</th>
                <th>Your Answer</th>
                <th>Gabarito</th>
                <th>Score</th>
            </tr>

            <?php
            $i = 0;
            $num = $denom = 0;
            foreach( $postmeta[0] as $meta )
            {

              $content = apply_filters( 'the_content', get_post_field( 'post_content', $meta['soundlush_question_id'] ) );
              $answer  = ( $meta['soundlush_user_answer'] >0 )? chr(96 + $meta['soundlush_user_answer'] ) : "--";
              $score   = $meta['soundlush_question_level'] * $meta['soundlush_multiplier'];
              $num += $score;
              $denom += $meta['soundlush_question_level'];

              $i++;
              $html  = '<tr>';
              $html .= '<td>' . $i . '</td>';
              $html .= '<td>' . wp_strip_all_tags($content) . '</td>';
              $html .= '<td>' . $answer . '</td>';
              $html .= '<td>' . (( $meta['soundlush_multiplier'] == 1 )? '<span class="text-success">Correct</span>' : '<span class="text-danger"">Incorrect</span>') . '</td>';
              $html .= '<td>' . $score . '/' . $meta['soundlush_question_level'] . '</td>';
              $html .= '</tr>';

              echo $html;

            }
            ?>

        </table>


        <p>Your Total Score: <?php echo get_post_meta( $lastquiz[0]->ID, '_soundlush_quiz_grade', true ); ?>% (<?php echo $num?>/<?php echo $denom ?>)</p>

        <p> Would you like to take it again and try to improve your score?</br>
            You will have <strong><?php echo $atts['time'] ?> minutes</strong> to complete your quiz after clicking the <strong>"Retake Quiz"</strong> button</p>
        <p class="text-danger"> IMPORTANT: Your latest result for this quiz will be replaced</p>

        <button id="soundlush_generate_quiz" class="btn btn-accent" data-id="<?php echo get_the_id() ?>" data-user="<?php echo get_current_user_id() ?>" data-qty="<?php echo $atts['qty'] ?>" data-time="<?php echo $atts['time'] ?>" data-pool="<?php echo $atts['pool']?>" >Retake Quiz</button>
    </div>
    <p id="time"></p>
    <div id="the-quiz"></div>

<?php else: ?>

    <div class="quiz-page">
        <h3> Your quiz is about to start!</h3>
        <p> You will have <strong><?php echo $atts['time'] ?> minutes</strong> to complete your quiz after clicking the <strong>"Take Quiz"</strong> button</p>

        <button id="soundlush_generate_quiz" class="btn btn-accent" data-id="<?php echo get_the_id() ?>" data-user="<?php echo get_current_user_id() ?>" data-qty="<?php echo $atts['qty'] ?>" data-time="<?php echo $atts['time'] ?>" data-pool="<?php echo $atts['pool']?>" >Take Quiz</button>
    </div>
    <p id="time"></p>
    <div id="the-quiz"></div>

<?php endif ?>
