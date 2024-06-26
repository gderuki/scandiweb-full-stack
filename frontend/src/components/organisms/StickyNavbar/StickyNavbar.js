import React, { Component } from 'react';
import './StickyNavbar.css';
import Button from 'atoms/Button';
import CartIcon from 'icons/CartIcon';
import { withRouter, Link } from 'react-router-dom';

class StickyNavbar extends Component {
  constructor(props) {
    super(props);
    this.state = { scrolled: false, highZIndex: false }; 
  }

  handleScroll = () => {
    const isScrolled = window.scrollY > 0;
    if (isScrolled !== this.state.scrolled) {
      this.setState({ scrolled: isScrolled });
    }
  };

  setHighZIndex = () => {
    this.setState({ highZIndex: true });
  };

  resetZIndex = () => {
    this.setState({ highZIndex: false });
  };

  componentDidMount() {
    window.addEventListener('scroll', this.handleScroll);
    document.addEventListener('lightboxActivated', this.setHighZIndex);
    document.addEventListener('lightboxDeactivated', this.resetZIndex);
  }

  componentWillUnmount() {
    window.removeEventListener('scroll', this.handleScroll);
    document.removeEventListener('lightboxActivated', this.setHighZIndex);
    document.removeEventListener('lightboxDeactivated', this.resetZIndex);
  }

  render() {
    const { scrolled, highZIndex } = this.state;
    const currentPath = this.props.location.pathname;
    const currentCategory = currentPath.split('/')[2];
    const navbarStyle = highZIndex ? { zIndex: 0 } : {};

    return (
      <nav
        className={`sticky-navbar ${scrolled ? 'scrolled' : ''}`}
        style={navbarStyle}
      >
        <div className="navbar-container">
          <div className="menu-items">
            {['women', 'men', 'kids'].map((cat) => (
              <Link
                key={cat}
                to={`/category/${cat}`}
                className={currentCategory === cat ? 'active' : ''}
              >
                {cat.charAt(0).toUpperCase() + cat.slice(1)}
              </Link>
            ))}
          </div>
          <div className="cart-container">
            <Button
              className="cart-button"
              icon={<CartIcon />}
              onClick={() => console.log('Cart button clicked')}
            />
          </div>
        </div>
      </nav>
    );
  }
}

export default withRouter(StickyNavbar);