import React, { Component, MouseEvent } from 'react'
import { observer } from 'mobx-react'
import CardStore from '~components/Card/src/stores'
import styled from 'styled-components'
import { device } from '~components/Style/src/components/devices'
import { COLOR_DARK, GUTTER } from '~components/Config/src'
import getElementOffsetTop from '~components/Helper/src/components/get-element-offset-top'

const StyledNav = styled.div`
  position: fixed;
  bottom: 0;
  background: ${COLOR_DARK};
  padding: 0 ${GUTTER};
  left: 0;
  right: 0;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 3rem;
  line-height: 3rem;
`

const StyledNavLink = styled.a`
  white-space: nowrap;
  color: #ccc;
  padding: 0.3rem 0.5rem;
  border-right: 1px solid rgba(255, 255, 255, 0.05);

  @media ${device('tablet')} {
    padding: 0.3rem ${GUTTER};
  }

  :hover {
    background: #f8f8f8;
    color: ${COLOR_DARK};
    text-decoration: none;
    box-shadow: inset 0 -10px 10px rgba(0, 0, 0, 0.1),
      0 -5px 30px rgba(0, 0, 0, 0.3);
  }

  :last-child {
    border-right: 0;
  }
`

const StyledNavLinkTitle = styled.span`
  display: none;

  @media ${device('desktopSm')} {
    display: block;
  }
`

const StyledNavLinkTinyTitle = styled.span`
  display: block;

  @media ${device('desktopSm')} {
    display: none;
  }
`

@observer
class Nav extends Component {
  private onClick = (e: MouseEvent, id: string) => {
    e.preventDefault()

    const target = document.querySelector(`#${id}`) as HTMLElement

    if (!target) {
      return
    }

    history.pushState(null, '', `#${id}`)
    window.scrollTo(0, getElementOffsetTop(target) - 50)
  }

  public render() {
    return (
      <StyledNav>
        {CardStore.sortedCards.map(({ id, title, tinyTitle }) => {
          return (
            <StyledNavLink
              key={id}
              onClick={e => this.onClick(e, id)}
              href={`#${id}`}
            >
              <StyledNavLinkTitle>{title}</StyledNavLinkTitle>
              <StyledNavLinkTinyTitle>{tinyTitle}</StyledNavLinkTinyTitle>
            </StyledNavLink>
          )
        })}
      </StyledNav>
    )
  }
}

export default Nav
