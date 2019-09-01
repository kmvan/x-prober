import React, { Component, MouseEvent } from 'react'
import { observer } from 'mobx-react'
import CardStore from '~components/Card/src/stores'
import styled from 'styled-components'
import { device } from '~components/Style/src/components/devices'
import { DARK_COLOR, GUTTER } from '~components/Config/src'
import BootstrapStore from '~components/Bootstrap/src/stores'

const NavContainer = styled.div`
  position: fixed;
  bottom: 0;
  background: ${DARK_COLOR};
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

const NavLink = styled.a`
  white-space: nowrap;
  color: #ccc;
  padding: 0.3rem 0.5rem;
  border-right: 1px solid #ffffff0d;
  @media ${device('tablet')} {
    padding: 0.3rem ${GUTTER};
  }
  :hover {
    background: #f8f8f8;
    color: ${DARK_COLOR};
    text-decoration: none;
    box-shadow: inset 0 -10px 10px rgba(0, 0, 0, 0.1),
      0 -5px 30px rgba(0, 0, 0, 0.3);
  }
  :last-child {
    border-right: 0;
  }
`

const NavLinkTitle = styled.span`
  display: none;
  @media ${device('desktopSm')} {
    display: block;
  }
`

const NavLinkTinyTitle = styled.span`
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
    BootstrapStore.appContainer &&
      BootstrapStore.appContainer.scrollTo(0, target.offsetTop - 40)
  }

  public render() {
    return (
      <NavContainer>
        {CardStore.sortedCards.map(({ id, title, tinyTitle }) => {
          return (
            <NavLink
              key={id}
              onClick={e => this.onClick(e, id)}
              href={`#${id}`}
            >
              <NavLinkTitle>{title}</NavLinkTitle>
              <NavLinkTinyTitle>{tinyTitle}</NavLinkTinyTitle>
            </NavLink>
          )
        })}
      </NavContainer>
    )
  }
}

export default Nav
