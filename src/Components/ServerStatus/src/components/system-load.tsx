import CardGrid from '@/Card/src/components/card-grid'
import React from 'react'
import store from '../stores'
import styled from 'styled-components'
import template from '@/Helper/src/components/template'
import { device } from '@/Style/src/components/devices'
import { gettext } from '@/Language/src'
import { GUTTER } from '@/Config/src'
import { rgba } from 'polished'
interface StyledSysLoadGroupProps {
  isCenter: boolean
}
export const StyledSysLoadGroup = styled.div<StyledSysLoadGroupProps>`
  display: flex;
  align-items: center;
  justify-content: center;
  @media ${device('tablet')} {
    justify-content: ${({ isCenter }) => (isCenter ? 'center' : 'flex-start')};
  }
`
export const StyledSysLoadGroupItem = styled.span`
  margin-right: 0.5rem;
  background: ${({ theme }) => rgba(theme.colorDark, 0.75)};
  color: ${({ theme }) => theme.colorGray};
  padding: calc(${GUTTER} / 10) calc(${GUTTER} / 1.5);
  border-radius: 10rem;
  font-family: Arial Black;
  text-shadow: ${({ theme }) => theme.textShadowWithDarkBg};
  box-shadow: inset 0 5px 10px ${({ theme }) => rgba(theme.colorDarkDeep, 0.3)};
  font-weight: 700;
  @media ${device('tablet')} {
    padding: calc(${GUTTER} / 10) ${GUTTER};
  }
`
interface SysLoadGroupProps {
  sysLoad: number[]
  isCenter: boolean
}
export const SysLoadGroup = ({ sysLoad, isCenter }: SysLoadGroupProps) => {
  const minutes = [1, 5, 15]
  const loadHuman = sysLoad.map((load, i) => {
    return {
      id: `${minutes[i]}minAvg`,
      load,
      text: template(gettext('${minute} minute average'), {
        minute: minutes[i],
      }),
    }
  })
  return (
    <StyledSysLoadGroup isCenter={isCenter}>
      {loadHuman.map(({ id, load, text }) => (
        <StyledSysLoadGroupItem key={id} title={text}>
          {load.toFixed(2)}
        </StyledSysLoadGroupItem>
      ))}
    </StyledSysLoadGroup>
  )
}
interface SystemLoadProps {
  isCenter?: boolean
}
const SystemLoad = ({ isCenter = false }: SystemLoadProps) => {
  return (
    <CardGrid name={gettext('System load')} tablet={[1, 1]}>
      <SysLoadGroup isCenter={isCenter} sysLoad={store.sysLoad} />
    </CardGrid>
  )
}
export default SystemLoad
