import React from 'react';
import styles from './AppHeader.module.css';

const AppHeader = () => {
  return (
    <header className={styles.header}>
      <div className={styles.logoContainer}>
        <img 
          src="./favicon.svg" // Replace with your logo path
          alt="App Logo" 
          className={styles.logo}
        />
        <h1 className={styles.appName}>Break-It</h1>
      </div>
      <p className={styles.tagline}>Your best family tasks management experience</p>
    </header>
  );
};

export default AppHeader;