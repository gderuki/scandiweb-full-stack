import React, { Component } from 'react';
import './StickyNavbar.css';
import Button from 'components/atoms/Button';
import CartIcon from 'icons/CartIcon';

class StickyNavbar extends Component {
  constructor(props) {
    super(props);
    this.state = { scrolled: false, category: 'women' };
  }

  handleCategorySelect = (category) => {
    this.setState({ category });
    this.props.onCategorySelect(category);
  };

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
    return (
      <nav className={`sticky-navbar ${this.state.scrolled ? 'scrolled' : ''}`}>
        <div className="navbar-container">
          <div className="menu-items">
            {['women', 'men', 'kids'].map((cat) => (
              <button
                key={cat}
                onClick={() => this.handleCategorySelect(cat)}
                className={this.state.category === cat ? 'active' : ''}
              >
                {cat.charAt(0).toUpperCase() + cat.slice(1)}
              </button>
            ))}
          </div>
          <div className="cart-button">
            <Button icon={<CartIcon />} onClick={() => console.log('Cart button clicked')} />
          </div>
        </div>
      </nav>
    );
  }
}

export default StickyNavbar;