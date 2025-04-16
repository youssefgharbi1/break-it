import { Link } from 'react-router-dom';
import styles from './HomePage.module.css';

const HomePage = () => {
  return (
    <div className={styles.container}>
      {/* Hero Section */}
      <section className={styles.hero}>
        <div className={styles.heroContent}>
          <h1 className={styles.headline}>
            Organize your life with <span className={styles.highlight}>Bloom</span>
          </h1>
          <p className={styles.subheadline}>
            A beautiful productivity app to manage tasks, habits, and goals
          </p>
          <div className={styles.ctaButtons}>
            <Link to="/register" className={styles.primaryButton}>
              Get Started
            </Link>
            <Link to="/login" className={styles.secondaryButton}>
              Sign In
            </Link>
          </div>
        </div>
        <div className={styles.heroImage}>
          {/* Placeholder for app screenshot */}
          <div className={styles.mockup}></div>
        </div>
      </section>

      {/* Features Section */}
      <section className={styles.features}>
        <h2 className={styles.sectionTitle}>Why Choose Bloom</h2>
        <div className={styles.featureGrid}>
          <div className={styles.featureCard}>
            <div className={styles.featureIcon} style={{ backgroundColor: '#F8D49B' }}>
              ✨
            </div>
            <h3>Simple & Intuitive</h3>
            <p>Designed for effortless task management</p>
          </div>
          <div className={styles.featureCard}>
            <div className={styles.featureIcon} style={{ backgroundColor: '#B9CC95' }}>
              🔄
            </div>
            <h3>Habit Tracking</h3>
            <p>Build positive routines with streaks</p>
          </div>
          <div className={styles.featureCard}>
            <div className={styles.featureIcon} style={{ backgroundColor: '#E984A2' }}>
              🌸
            </div>
            <h3>Calming Design</h3>
            <p>Enjoy our soothing color palette</p>
          </div>
        </div>
      </section>

      {/* Testimonial Section */}
      <section className={styles.testimonial}>
        <blockquote>
          "Bloom transformed how I organize my day. It's beautiful and actually makes me want to use it!"
        </blockquote>
        <div className={styles.author}>- Sarah, Teacher</div>
      </section>
    </div>
  );
};

export default HomePage;