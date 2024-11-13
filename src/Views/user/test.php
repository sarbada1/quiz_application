<!-- Main content container -->
<div class="test-container">
    <?php if (!empty($programs)) : ?>
        <div class="programs-grid">
            <?php foreach ($programs as $program) : ?>
                <div class="program-card">
                    <h2><?php echo $program['name']; ?></h2>
                    <p class="description"><?php echo $program['description']; ?></p>
                    <div class="button-group">
                      <button class="primary w-100"> <a href="/test/<?php echo $program['slug']; ?>" >Mock Test</a>
                      </button> 

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p class="no-programs">No test programs available.</p>
    <?php endif; ?>
</div>

  