import React, { Component, MouseEvent } from 'react'
import { observer } from 'mobx-react'
import CardStore from '~components/Card/src/stores'
import styled from 'styled-components'
import { device } from '~components/Style/src/components/devices'
import {
  COLOR_DARK,
  GUTTER,
  COLOR_GRAY,
  TEXT_SHADOW_WITH_DARK_BG,
} from '~components/Config/src'
import getElementOffsetTop from '~components/Helper/src/components/get-element-offset-top'
import { rgba } from 'polished'

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
  justify-content: flex-start;
  height: 3rem;
  line-height: 3rem;
  overflow-x: auto;
  @media ${device('mobileMd')} {
    overflow-x: unset;
    justify-content: center;
  }
`

const StyledNavLink = styled.a`
  position: relative;
  white-space: nowrap;
  color: ${COLOR_GRAY};
  padding: 0 0.5rem;
  border-right: 1px solid ${rgba(COLOR_GRAY, 0.05)};
  text-shadow: ${TEXT_SHADOW_WITH_DARK_BG};

  @media ${device('tablet')} {
    padding: 0 ${GUTTER};
  }

  :hover {
    background: linear-gradient(${COLOR_GRAY}, #fff);
    color: ${COLOR_DARK};
    text-decoration: none;
    box-shadow: inset 0 -10px 10px ${rgba(COLOR_DARK, 0.1)},
      0 -5px 30px ${rgba(COLOR_DARK, 0.3)};
  }
  :focus,
  :active {
    text-decoration: none;
    color: ${COLOR_DARK};
    background: ${rgba(COLOR_GRAY, 0.85)};
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
        {CardStore.cards.map(({ id, title, tinyTitle, enabled = true }) => {
          if (!enabled) {
            return null
          }

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
