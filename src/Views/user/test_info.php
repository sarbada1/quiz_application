<!-- Main content container -->
<div class="test-mock-container">
    <?php if (!empty($mocktests)) : ?>
        <div class="programs-grid">
            <?php foreach ($mocktests as $mocktest) : ?>
                <div class="program-card">
                  <a href="/mocktest/<?=$mocktest['slug']?>"> <h2><?php echo $mocktest['name']; ?></h2></a> 

                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p class="no-programs">No test programs available.</p>
    <?php endif; ?>
</div>

  