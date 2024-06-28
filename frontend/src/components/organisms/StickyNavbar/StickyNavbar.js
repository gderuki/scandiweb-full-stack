import React, { Component } from 'react';
import './StickyNavbar.css';
import Button from 'atoms/Button';
import CartIcon from 'icons/CartIcon';
import { withRouter, Link } from 'react-router-dom';
import CartOverlay from 'organisms/CartOverlay';
import withCart from 'hoc/withCart';

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
    document.addEventListener('BIG_IMAGE_OPENED', this.setHighZIndex);
    document.addEventListener('BIG_IMAGE_CLOSED', this.resetZIndex);
  }

  componentWillUnmount() {
    window.removeEventListener('scroll', this.handleScroll);
    document.removeEventListener('BIG_IMAGE_OPENED', this.setHighZIndex);
    document.removeEventListener('BIG_IMAGE_CLOSED', this.resetZIndex);
  }

  toggleCartOverlay = () => {
    this.setState(prevState => ({ isCartOverlayVisible: !prevState.isCartOverlayVisible }));
  };

  render() {
    const { scrolled, highZIndex } = this.state;
    const currentPath = this.props.location.pathname;
    const currentCategory = currentPath.split('/')[2];
    const navbarStyle = highZIndex ? { zIndex: 0 } : {};
    const { isCartOverlayVisible, toggleCartOverlay, cartItems } = this.props;

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
              onClick={toggleCartOverlay}
            />
            {isCartOverlayVisible
              &&
              <CartOverlay
                items={cartItems}
                onClose={toggleCartOverlay}
              />
            }
          </div>
        </div>
      </nav>
    );
  }
}

export default withCart(withRouter(StickyNavbar));