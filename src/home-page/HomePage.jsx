import { Link } from 'react-router-dom';
import styles from './HomePage.module.css';
import flower from '../../public/flower.svg';
import eye from '../../public/eye.svg';
import spark from '../../public/sparkles.svg';

const HomePage = () => {
  return (
    <div className={styles.container}>
      {/* Hero Section */}
      <section className={styles.hero}>
        <div className={styles.heroContent}>
          <h1 className={styles.headline}>
            Organize family chores with <span className={styles.highlight}>BREAK-IT</span>
          </h1>
          <p className={styles.subheadline}>
            A beautiful productivity app Designed to suit tasks management, progress tracking and improving your family's productivity.
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
        <h2 className={styles.sectionTitle}>Why Choose Break-it</h2>
        <div className={styles.featureGrid}>
          <div className={styles.featureCard}>
            <div className={styles.featureIcon} style={{ backgroundColor: '#F8D49B' }}>
              <img src={spark} className={styles.featureIconImg} />
            </div>
            <h3>Simple & Intuitive</h3>
            <p>Designed for effortless task management</p>
          </div>
          <div className={styles.featureCard}>
            <div className={styles.featureIcon} style={{ backgroundColor: '#B9CC95' }}>
              <img src={eye} className={styles.featureIconImg} />
            </div>
            <h3>Habit Tracking</h3>
            <p>Build positive routines with streaks</p>
          </div>
          <div className={styles.featureCard}>
            <div className={styles.featureIcon} style={{ backgroundColor: '#E984A2' }}>
              <img src={flower} className={styles.featureIconImg} />
            </div>
            <h3>Calming Design</h3>
            <p>Enjoy our soothing color palette</p>
          </div>
        </div>
      </section>

      {/* Testimonial Section */}
      <section className={styles.testimonial}>
        <blockquote>
          "Break-it transformed how I organize my day for my family. It's beautiful and actually makes my kids
           enjoy "boring" and tedious tasks!"
        </blockquote>
        <div className={styles.author}>- Sarah, mother and teacher</div>
      </section>
    </div>
  );
};

export default HomePage;