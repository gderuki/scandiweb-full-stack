import React, { Component } from 'react';
import './StickyNavbar.css';
import Button from 'components/atoms/Button';
import CartIcon from 'icons/CartIcon';
import { withRouter, Link } from 'react-router-dom';

class StickyNavbar extends Component {
  constructor(props) {
    super(props);
    this.state = { scrolled: false };
  }

  handleScroll = () => {
    const isScrolled = window.scrollY > 0;
    if (isScrolled !== this.state.scrolled) {
      this.setState({ scrolled: isScrolled });
    }
  };

  componentDidMount() {
    window.addEventListener('scroll', this.handleScroll);
  }

  componentWillUnmount() {
    window.removeEventListener('scroll', this.handleScroll);
  }

  render() {
    const currentPath = this.props.location.pathname;
    const currentCategory = currentPath.split('/')[2];

    return (
      <nav className={`sticky-navbar ${this.state.scrolled ? 'scrolled' : ''}`}>
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
          <div className="cart-">
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