import React from 'react'
import styled from 'styled-components'
import formatBytes from '@/Helper/src/components/format-bytes'
import gradientColors from '@/Helper/src/components/gradient'
import { GUTTER } from '@/Config/src'
import { rgba, linearGradient } from 'polished'

export interface ProgressBarProps {
  title?: string
  value: number
  max: number
  isCapacity: boolean
  percentTag?: string
  left?: string
}

const StyledProgressBar = styled.div`
  position: relative;
`

const StyledProgressOverview = styled.div`
  position: absolute;
  right: ${GUTTER};
  bottom: 0;
  z-index: 1;
  font-weight: 700;
  color: ${({ theme }) => theme.colorGray};
  text-shadow: ${({ theme }) => theme.textShadowWithDarkBg};
  line-height: 2rem;
  font-family: 'Arial Black';
`
const StyledProgressPercent = styled(StyledProgressOverview)`
  left: ${GUTTER};
  right: auto;
`

const StyledProgressShell = styled.div`
  position: relative;
  display: flex;
  width: 100%;
  height: 2rem;
  background: ${({ theme }) => theme.colorDark};
  border-radius: ${GUTTER};
  box-shadow: inset 0 10px 20px ${({ theme }) => rgba(theme.colorDarkDeep, 0.3)};
  padding: 0.3rem;
`

interface StyledProgressValueProps {
  percent: number
}

const StyledProgressValue = styled.div.attrs(
  ({ percent }: StyledProgressValueProps) => ({
    style: {
      backgroundColor: `#${
        gradientColors('#00cc00', '#ef2d2d')[~~percent - 1]
      }`,
      width: `${percent}%`,
    },
  })
)<StyledProgressValueProps>`
  position: relative;
  transition: width 0.5s;
  border-radius: ${GUTTER};
  background: #0c0;
  box-shadow: inset 0 -5px 10px ${({ theme }) => rgba(theme.colorDarkDeep, 0.4)},
    0 5px 10px 0 ${({ theme }) => rgba(theme.colorDarkDeep, 0.3)};
  overflow: hidden;

  ::after,
  ::before {
    position: absolute;
    content: '';
    top: 0;
    height: 50%;
    width: 100%;
    border-radius: 0 0 50% 50%;
    ${linearGradient({
      colorStops: ['#fff', rgba('#fff', 0.3)],
      fallback: 'transparent',
    })};
    opacity: 0.3;
  }
  ::before {
    ${linearGradient({
      colorStops: [rgba('#fff', 0.1), rgba('#fff', 0.5), rgba('#fff', 0.1)],
      toDirection: 'to top right',
      fallback: 'transparent',
    })};
    opacity: 1;
    height: 1px;
    border-radius: 0;
  }
`

const ProgressBar = ({
  title = '',
  value,
  max,
  isCapacity,
  percentTag = '%',
  left = '',
}: ProgressBarProps) => {
  const percent = max === 0 || value === 0 ? 0 : (value / max) * 100
  const overview = isCapacity
    ? `${formatBytes(value)} / ${formatBytes(max)}`
    : `${value.toFixed(1)}${percentTag} / ${max}${percentTag}`
  const overviewPercent = left ? left : `${percent.toFixed(1)}${percentTag}`

  return (
    <StyledProgressBar title={title}>
      <StyledProgressPercent>{overviewPercent}</StyledProgressPercent>
      <StyledProgressOverview>{overview}</StyledProgressOverview>
      <StyledProgressShell>
        <StyledProgressValue percent={percent} />
      </StyledProgressShell>
    </StyledProgressBar>
  )
}

export default ProgressBar
