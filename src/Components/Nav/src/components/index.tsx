import CardStore from '@/Card/src/stores'
import { ANIMATION_DURATION_SC, GUTTER } from '@/Config/src'
import { device } from '@/Style/src/components/devices'
import getElementOffsetTop from '@/Utils/src/components/get-element-offset-top'
import { observer } from 'mobx-react-lite'
import React, { MouseEvent, useCallback } from 'react'
import styled, { keyframes } from 'styled-components'
const slideUp = keyframes`
  from{
    transform: translate3d(0, 100%, 0);
  }
  to{
    transform: translate3d(0, 0, 0);
  }
`
const StyledNav = styled.div`
  position: fixed;
  bottom: 0;
  background: ${({ theme }) => theme['nav.bg']};
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
  @media ${device('mobileLg')} {
    overflow-x: unset;
    justify-content: center;
  }
`
const StyledNavLink = styled.a`
  position: relative;
  white-space: nowrap;
  color: ${({ theme }) => theme['nav.fg']};
  padding: 0 0.5rem;
  border-right: 1px solid ${({ theme }) => theme['nav.border']};
  animation: ${slideUp} ${ANIMATION_DURATION_SC}s;
  animation-fill-mode: forwards;
  @media ${device('tablet')} {
    padding: 0 ${GUTTER};
  }
  :hover,
  :focus,
  :active {
    background: ${({ theme }) => theme['nav.hover.bg']};
    color: ${({ theme }) => theme['nav.hover.fg']};
    text-decoration: none;
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
const Nav = observer(() => {
  const onClick = useCallback(
    (e: MouseEvent<HTMLAnchorElement>, id: string) => {
      e.preventDefault()
      const target = document.querySelector(`#${id}`) as HTMLElement
      if (!target) {
        return
      }
      history.pushState(null, '', `#${id}`)
      window.scrollTo(0, getElementOffsetTop(target) - 50)
    },
    []
  )
  return (
    <StyledNav>
      {CardStore.enabledCards.map(
        ({ id, title, tinyTitle, enabled = true }) => {
          if (!enabled) {
            return null
          }
          return (
            <StyledNavLink
              key={id}
              onClick={(e) => onClick(e, id)}
              href={`#${id}`}>
              <StyledNavLinkTitle>{title}</StyledNavLinkTitle>
              <StyledNavLinkTinyTitle>{tinyTitle}</StyledNavLinkTinyTitle>
            </StyledNavLink>
          )
        }
      )}
    </StyledNav>
  )
})
export default Nav
