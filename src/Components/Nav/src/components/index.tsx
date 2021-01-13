import CardStore from '@/Card/src/stores'
import getElementOffsetTop from '@/Helper/src/components/get-element-offset-top'
import React, { MouseEvent, useCallback } from 'react'
import styled, { keyframes } from 'styled-components'
import { ANIMATION_DURATION_SC, GUTTER } from '@/Config/src'
import { device } from '@/Style/src/components/devices'
import { observer } from 'mobx-react-lite'
import { rgba } from 'polished'
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
  background: ${({ theme }) => theme.colorDark};
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
  color: ${({ theme }) => theme.colorGray};
  padding: 0 0.5rem;
  border-right: 1px solid ${({ theme }) => rgba(theme.colorGray, 0.05)};
  text-shadow: ${({ theme }) => theme.textShadowWithDarkBg};
  animation: ${slideUp} ${ANIMATION_DURATION_SC}s;
  animation-fill-mode: forwards;
  @media ${device('tablet')} {
    padding: 0 ${GUTTER};
  }
  :hover {
    background: linear-gradient(${({ theme }) => theme.colorGray}, #fff);
    color: ${({ theme }) => theme.colorDark};
    text-decoration: none;
    box-shadow: inset 0 -10px 10px ${({ theme }) => rgba(theme.colorDarkDeep, 0.1)},
      0 -5px 30px ${({ theme }) => rgba(theme.colorDarkDeep, 0.3)};
    text-shadow: ${({ theme }) => theme.textShadowWithLightBg};
  }
  :focus,
  :active {
    text-decoration: none;
    color: ${({ theme }) => theme.colorDark};
    background: ${({ theme }) => rgba(theme.colorGray, 0.85)};
    text-shadow: ${({ theme }) => theme.textShadowWithLightBg};
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
  const onClick = useCallback((e: MouseEvent, id: string) => {
    e.preventDefault()
    const target = document.querySelector(`#${id}`) as HTMLElement
    if (!target) {
      return
    }
    history.pushState(null, '', `#${id}`)
    window.scrollTo(0, getElementOffsetTop(target) - 50)
  }, [])
  return (
    <StyledNav>
      {CardStore.cards.map(({ id, title, tinyTitle, enabled = true }) => {
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
      })}
    </StyledNav>
  )
})
export default Nav
