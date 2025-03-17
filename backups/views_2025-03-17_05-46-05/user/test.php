<!-- Main content container -->
<div class="test-container">
    <?php if (!empty($programs)) : ?>
        <div class="programs-grid">
            <?php foreach ($programs as $program) : ?>
                <div class="program-card">
                    <h2><?php echo $program['title']; ?></h2>
              
                    <div class="button-group flex">

                      </button> 
                      <button class="primary w-50 ml-5"> 
                        <a href="/test/<?php echo $program['slug']; ?>">Mock Test</a>
                      </button> 
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p class="no-programs">No test programs available.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/auth/login.php'; ?>
<?php include __DIR__ . '/auth/register.php'; ?>