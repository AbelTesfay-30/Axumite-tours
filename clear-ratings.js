// Clear any problematic ratings from localStorage
(function clearProblematicRatings() {
    console.log('Clearing problematic ratings...');
    
    // Get existing ratings
    let ratings = JSON.parse(localStorage.getItem('axumiteRatings') || '[]');
    console.log('Current ratings:', ratings);
    
    // Remove any rating by Samiel (case insensitive)
    const originalCount = ratings.length;
    ratings = ratings.filter(rating => 
        !rating.name.toLowerCase().includes('samiel')
    );
    
    const removedCount = originalCount - ratings.length;
    console.log(`Removed ${removedCount} rating(s) by Samiel`);
    
    // Save cleaned ratings back
    localStorage.setItem('axumiteRatings', JSON.stringify(ratings));
    
    // Also clear modal flags to ensure proper behavior
    localStorage.removeItem('ratingModalShown');
    localStorage.removeItem('contactRatingModalShown');
    
    console.log('Cleanup completed. Final ratings:', ratings);
    console.log('Rating system is ready for use.');
})();
